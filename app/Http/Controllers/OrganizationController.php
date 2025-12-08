<?php

namespace App\Http\Controllers;

use App\DTOs\CoordinatesDTO;
use App\Http\Requests\IdRequest;
use App\Http\Requests\NearbyOrganizationsRequest;
use App\Http\Requests\TitleRequest;
use App\Http\Resources\OrganizationResource;
use App\Services\OrganizationService;
use Illuminate\Http\JsonResponse;

class OrganizationController extends Controller
{
    public function __construct(
        private readonly OrganizationService $service
    ) {}

    /**
     * Display the specified resource.
     */
    public function show(IdRequest $request, string $id): JsonResponse
    {
        $organization = $this->service->getOrganization($id);

        return new JsonResponse(new OrganizationResource($organization));
    }

    public function nearby(NearbyOrganizationsRequest $request): JsonResponse
    {
        $coordinates = new CoordinatesDTO(
            $request->get('lat'),
            $request->get('lng'),
            $request->get('radius') ?? 10
        );

        $organizations = $this->service->getNearby($coordinates);

        return new JsonResponse(OrganizationResource::collection($organizations));
    }

    public function search(TitleRequest $request): JsonResponse
    {
        $title = $request->get('title');
        $organizations = $this->service->getByOrganisationsTitle($title);

        return new JsonResponse(OrganizationResource::collection($organizations));
    }

    public function byBuilding(IdRequest $request, string $buildingId): JsonResponse
    {
        $organizations = $this->service->getByBuilding($buildingId);

        return new JsonResponse(OrganizationResource::collection($organizations));
    }

    public function byActivity(IdRequest $request, string $activityId): JsonResponse
    {
        $organizations = $this->service->getByActivity($activityId);

        return new JsonResponse(OrganizationResource::collection($organizations));
    }

    public function byActivityTitle(TitleRequest $request): JsonResponse
    {
        $organizations = $this->service->getByActivityTitle($request->get('title'));

        return new JsonResponse(OrganizationResource::collection($organizations));
    }
}
