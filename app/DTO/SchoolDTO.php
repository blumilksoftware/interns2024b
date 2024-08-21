<?php

declare(strict_types=1);

namespace App\DTO;

class SchoolDTO
{
    public function __construct(
        public string $name,
        public string $city,
        public string $street,
        public string $building_number,
        public string $apartment_number,
        public string $zip_code,
    ) {}

    public static function createFromArray(array $data): self
    {
        $member = new self(
            $data["nazwa"],
            $data["miejscowosc"],
            $data["ulica"],
            $data["numerBudynku"],
            $data["numerLokalu"],
            $data["kodPocztowy"],
        );

        return $member;
    }
}
