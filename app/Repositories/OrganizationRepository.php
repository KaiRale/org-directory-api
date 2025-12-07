<?php

namespace App\Repositories;

use App\DTOs\OrganizationFiltersDTO;
use App\Models\Activity;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Collection;

class OrganizationRepository implements OrganizationRepositoryInterface
{
    private const DEFAULT_RELATIONS = ['building', 'phones', 'activities'];

    public function findById(int $id)
    {
        return Organization::with(self::DEFAULT_RELATIONS)->findOrFail($id);
    }

    public function search(OrganizationFiltersDTO $filters): Collection
    {
        $query = Organization::with(self::DEFAULT_RELATIONS);

        if ($filters->title) {
            $query->where('title', 'like', '%' . $filters->title . '%');
        }

        if ($filters->buildingId) {
            $query->where('building_id', $filters->buildingId);
        }

        if ($filters->activityId) {
            $query->whereHas('activities', function ($q) use ($filters) {
                $q->whereIn('id', $this->getActivityIdsWithDescendants($filters->activityId));
            });
        }

        if ($filters->activityTitle) {
            $activityIds = $this->getActivityIdsByTitle($filters->activityTitle);
            if ($activityIds->isNotEmpty()) {
                $query->whereHas('activities', function ($q) use ($activityIds) {
                    $q->whereIn('id', $activityIds);
                });
            }
        }

        if ($filters->coordinates) {
            $query->whereHas('building', function ($q) use ($filters) {
                $q->whereRaw("ST_Distance_Sphere(POINT(longitude, latitude), POINT(?, ?)) <= ?", [
                    $filters->coordinates->longitude,
                    $filters->coordinates->latitude,
                    $filters->coordinates->radius // конвертация в метры
                ]);
            });
        }

        return $query->get();
    }

    public function findByBuildingId(int $buildingId): Collection
    {
        return Organization::with(self::DEFAULT_RELATIONS)
            ->where('building_id', $buildingId)
            ->get();
    }

    public function findByActivityId(int $activityId): Collection
    {
        $activityIds = $this->getActivityIdsWithDescendants($activityId);

        return Organization::with(self::DEFAULT_RELATIONS)
            ->whereHas('activities', function ($query) use ($activityIds) {
                $query->whereIn('id', $activityIds);
            })
            ->get();
    }

    public function findByActivityTitle(string $title): Collection
    {
        $activityIds = $this->getActivityIdsByTitle($title);

        if ($activityIds->isEmpty()) {
            return new Collection();
        }

        return Organization::with(self::DEFAULT_RELATIONS)
            ->whereHas('activities', function ($query) use ($activityIds) {
                $query->whereIn('id', $activityIds);
            })
            ->get();
    }

    public function findByCoordinates(float $lat, float $lng, float $radius): Collection
    {
        return Organization::with(self::DEFAULT_RELATIONS)
            ->whereHas('building', function ($query) use ($lng, $lat, $radius) {
                $query->whereRaw("ST_Distance_Sphere(POINT(longitude, latitude), POINT(?, ?)) <= ?", [
                    $lng, $lat, $radius * 1000
                ]);
            })
            ->get();
    }

    private function getActivityIdsWithDescendants(int $activityId): Collection
    {
        $activity = Activity::with('children.children')->findOrFail($activityId);
        return $this->extractActivityIds($activity);
    }

    private function getActivityIdsByTitle(string $title): Collection
    {
        $activities = Activity::where('title', 'like', "%{$title}%")->get();

        $allIds = collect();
        foreach ($activities as $activity) {
            $allIds = $allIds->merge($this->extractActivityIds($activity));
        }

        return $allIds->unique();
    }

    private function extractActivityIds($activity, int $level = 0): Collection
    {
        $ids = collect([$activity->id]);

        // Ограничение вложенности 3 уровня
        if ($level < 3 && $activity->children) {
            foreach ($activity->children as $child) {
                $ids = $ids->merge($this->extractActivityIds($child, $level + 1));
            }
        }

        return $ids;
    }
}
