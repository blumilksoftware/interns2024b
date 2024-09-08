<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up(): void
    {
        Schema::table("quizzes", function (Blueprint $table): void {
            $table->renameColumn("name", "title");
        });
    }

    public function down(): void
    {
        Schema::table("quizzes", function (Blueprint $table): void {
            $table->renameColumn("title", "name");
        });
    }
};
