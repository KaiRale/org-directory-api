<?php

namespace App\Repositories;

use App\Models\Organization;
use Illuminate\Database\Eloquent\Collection;

interface OrganizationRepositoryInterface
{
    public function findById(int $id): ?Organization;
    public function findByOrganisationsTitle(string $title): Collection;
    public function findByBuildingId(int $buildingId): Collection;
    public function findByActivityId(int $activityId): Collection;
    public function findByActivityTitle(string $title): Collection;
    public function findByCoordinates(float $lat, float $lng, float $radius): Collection;
}
