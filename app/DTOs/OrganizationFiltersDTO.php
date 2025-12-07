<?php

namespace App\DTOs;

class OrganizationFiltersDTO
{
    public function __construct(
        public readonly ?string $title = null,
        public readonly ?int $buildingId = null,
        public readonly ?int $activityId = null,
        public readonly ?string $activityTitle = null,
        public readonly ?CoordinatesDTO $coordinates = null
    ) {}
}
