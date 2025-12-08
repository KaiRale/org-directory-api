<?php

namespace App\Services;

use App\DTOs\CoordinatesDTO;
use App\Models\Organization;
use App\Repositories\OrganizationRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

readonly class OrganizationService
{
    public function __construct(
        private OrganizationRepositoryInterface $repository
    ) {}

    public function getOrganization(int $id): ?Organization
    {
        return $this->repository->findById($id);
    }

    public function getByOrganisationsTitle(string $title): Collection
    {
        return $this->repository->findByOrganisationsTitle($title);
    }

    public function getByBuilding(int $buildingId): Collection
    {
        return $this->repository->findByBuildingId($buildingId);
    }

    public function getByActivity(int $activityId): Collection
    {
        return $this->repository->findByActivityId($activityId);
    }

    public function getByActivityTitle(string $title): Collection
    {
        return $this->repository->findByActivityTitle($title);
    }

    public function getNearby(CoordinatesDTO $coordinates): Collection
    {
        return $this->repository->findByCoordinates(
            $coordinates->latitude,
            $coordinates->longitude,
            $coordinates->radius
        );
    }
}
