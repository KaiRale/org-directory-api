<?php

namespace App\DTOs;

class CoordinatesDTO
{
    public function __construct(
        public readonly float $latitude,
        public readonly float $longitude,
        public readonly ?float $radius = 10
    ) {}
}
