<?php

namespace App\Repositories;

use App\Models\Activity;
use App\Models\Building;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as IlluminateCollection;

class OrganizationRepository implements OrganizationRepositoryInterface
{
    private const DEFAULT_RELATIONS = ['building', 'phones', 'activities'];

    public function findById(int $id): ?Organization
    {
        return Organization::with(self::DEFAULT_RELATIONS)->findOrFail($id);
    }

    public function findByCoordinates(float $lat, float $lng, float $radius): Collection
    {
        return Organization::with(self::DEFAULT_RELATIONS)
            ->whereHas('building', function ($query) use ($lng, $lat, $radius) {
                $query->whereRaw("
                    ST_Distance_Sphere(
                        POINT(longitude, latitude),
                        POINT(?, ?)
                    ) <= ?
                    ",
                    [$lng, $lat, $radius]
                );
            })
            ->get();
    }

    public function findByOrganisationsTitle(string $title): Collection
    {
        $query = Organization::with(self::DEFAULT_RELATIONS);
        $query->where('title', 'like', '%' . $title . '%');

        return $query->get();
    }

    public function findByBuildingId(int $buildingId): Collection
    {
        Building::findOrFail($buildingId);

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

    private function getActivityIdsByTitle(string $title): IlluminateCollection
    {
        $activities = Activity::where('title', 'like', "%{$title}%")->get();

        return $activities->pluck('id');
    }

    // getting the activity and its descendants
    private function getActivityIdsWithDescendants(int $activityId): IlluminateCollection
    {
        $activity = Activity::with('children.children')->findOrFail($activityId);

        return $this->extractActivityIds($activity);
    }

    private function extractActivityIds($activity, int $level = 0): IlluminateCollection
    {
        $ids = collect([$activity->id]);

        // the nesting limit is 3 levels
        if ($level < 3 && $activity->children) {
            foreach ($activity->children as $child) {
                $ids = $ids->merge($this->extractActivityIds($child, $level + 1));
            }
        }

        return $ids;
    }
}
