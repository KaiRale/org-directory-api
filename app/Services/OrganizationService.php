<?php

namespace App\Services;

use App\DTOs\CoordinatesDTO;
use App\DTOs\OrganizationFiltersDTO;
use App\Repositories\OrganizationRepositoryInterface;

class OrganizationService
{
    public function __construct(
        private readonly OrganizationRepositoryInterface $repository
    ) {}

    public function getOrganization(int $id)
    {
        return $this->repository->findById($id);
    }

    public function searchOrganizations(OrganizationFiltersDTO $filters)
    {
        return $this->repository->search($filters);
    }

    public function getByBuilding(int $buildingId)
    {
        return $this->repository->findByBuildingId($buildingId);
    }

    public function getByActivity(int $activityId)
    {
        return $this->repository->findByActivityId($activityId);
    }

    public function getByActivityTitle(string $title)
    {
        return $this->repository->findByActivityTitle($title);
    }

    public function getNearby(CoordinatesDTO $coordinates)
    {
        return $this->repository->findByCoordinates(
            $coordinates->latitude,
            $coordinates->longitude,
            $coordinates->radius
        );
    }
}
