<?php

declare(strict_types=1);

namespace App\DTO;

class SchoolDTO
{
    public function __construct(
        public string $name,
        public string $regon,
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
            $data["regon"],
            $data["miejscowosc"],
            $data["ulica"],
            $data["numerBudynku"],
            $data["numerLokalu"],
            $data["kodPocztowy"],
        );

        return $member;
    }

    public function toArray(): array
    {
        return [
            "name" => $this->name,
            "regon" => $this->regon,
            "city" => $this->city,
            "street" => $this->street,
            "building_number" => $this->buildingNumber,
            "apartment_number" => $this->apartmentNumber,
            "zip_code" => $this->zipCode,
        ];
    }
}
