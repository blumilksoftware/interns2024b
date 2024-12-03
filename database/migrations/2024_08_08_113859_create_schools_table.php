<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up(): void
    {
        Schema::create("schools", function (Blueprint $table): void {
            $table->bigIncrements("id")->unique();
            $table->string("regon");
            $table->string("name");
            $table->string("city");
            $table->string("street");
            $table->string("building_number");
            $table->string("apartment_number")->nullable();
            $table->string("zip_code");
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists("schools");
    }
};
