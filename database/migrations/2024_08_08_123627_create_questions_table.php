<?php

declare(strict_types=1);

use App\Models\Test;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up(): void
    {
        Schema::create("questions", function (Blueprint $table): void {
            $table->id();
            $table->timestamps();
            $table->text("text");
            $table->foreignIdFor(Test::class)->constrained()->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists("questions");
    }
};
