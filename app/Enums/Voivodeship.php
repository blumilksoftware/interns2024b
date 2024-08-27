<?php

declare(strict_types=1);

namespace App\Enums;

enum Voivodeship: string
{
    case LOWER_SILESIA = "DOLNOŚLĄSKIE";
    case KUYAVIA_POMERANIA = "KUJAWSKO-POMORSKIE";
    case LUBLIN = "LUBELSKIE";
    case LUBUSZ = "LUBUSKIE";
    case LODZ = "ŁÓDZKIE";
    case LESSER_POLAND = "MAŁOPOLSKIE";
    case MASOVIA = "MAZOWIECKIE";
    case OPOLE = "OPOLSKIE";
    case SUBCARPATHIA = "PODKARPACKIE";
    case PODLASKIE = "PODLASKIE";
    case POMERANIA = "POMORSKIE";
    case SILESIA = "ŚLĄSKIE";
    case HOLY_CROSS = "ŚWIĘTOKRZYSKIE";
    case WARMIA_MASURIA = "WARMIŃSKO-MAZURSKIE";
    case GREATER_POLAND = "WIELKOPOLSKIE";
    case WEST_POMERANIA = "ZACHODNIOPOMORSKIE";
}
