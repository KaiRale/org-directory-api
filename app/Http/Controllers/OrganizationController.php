<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Building;
use App\Models\Organization;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrganizationController extends Controller
{
    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $organization = Organization::with(
            [
                'building',
                'phones',
                'activities'
            ]
        )->findOrFail($id);

        return response()->json($organization);
    }

    public function nearby(Request $request): JsonResponse
    {
        $request->validate([
            'lat' => 'required|numeric|min:-90|max:90',
            'lng' => 'required|numeric|min:-180|max:180',
            'radius' => 'nullable|numeric|min:-90|max:90',
        ]);

        $lat = $request->query('lat');
        $lng = $request->query('lng');
        $radius = $request->query('radius', 10);

        $organizations = Organization::with([
            'building',
            'phones',
            'activities'
        ])
            ->whereHas('building', function ($query) use ($lat, $lng, $radius) {
                $query->whereRaw("
                    ST_Distance_Sphere(
                        POINT(longitude, latitude),
                        POINT(?, ?)
                    ) <= ?
                ", [$lng, $lat, $radius]);
            })
            ->get();

        return response()->json($organizations);
    }

    public function search(Request $request): JsonResponse
    {
        $request->validate([
            'title' => 'required|string|min:3',
        ]);

        $title = $request->query('title');

        $organizations = Organization::with([
            'building',
            'phones',
            'activities'
        ])
            ->where('title', 'like', '%' . $title . '%')
            ->get();

        return response()->json($organizations);
    }

    public function byBuilding(string $buildingId): JsonResponse
    {
        Building::findOrFail($buildingId);

        $organization = Organization::with([
            'building',
            'phones',
            'activities'
        ])
            ->where([
                'building_id' => $buildingId,
            ])->get();

        return response()->json($organization);
    }

    public function byActivity(string $activityId): JsonResponse
    {
        $activity = Activity::with('children')->findOrFail($activityId);
        $activityIds = $activity->getSelfAndDescendantIds();

        $organizations = Organization::with([
            'building',
            'phones',
            'activities'
        ])
            ->whereHas('activities', function ($query) use ($activityIds) {
                $query->whereIn('id', $activityIds);
            })
            ->get();

        return response()->json($organizations);

    }

    public function byActivityTitle(Request $request): JsonResponse
    {
        $request->validate([
            'title' => 'required|string|min:2',
        ]);

        $title = $request->query('title');

        $activities = Activity::findByTitleWithChildren($title);

        if ($activities->isEmpty()) {
            return response()->json(['message' => 'No activities found.']);
        }

        $activityIds = Activity::getSelfAndDescendantIdsForActivities($activities);

        $organizations = Organization::with([
            'building',
            'phones',
            'activities'
        ])
            ->whereHas('activities', function ($query) use ($activityIds) {
                $query->whereIn('id', $activityIds);
            })
            ->get();

        return response()->json($organizations);
    }
}
