<?php

declare(strict_types=1);

namespace App\DTO;

class SchoolDTO
{
    public function __construct(
        public string $name,
        public string $city,
        public string $street,
        public string $buildingNumber,
        public string $apartmentNumber,
        public string $zipCode,
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
