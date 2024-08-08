<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up(): void
    {
        Schema::create("tests", function (Blueprint $table): void {
            $table->id();
            $table->timestamps();
            $table->timestamp("locked_at")->nullable();
            $table->string("name");
        });
    }

    public function down(): void
    {
        Schema::dropIfExists("tests");
    }
};
