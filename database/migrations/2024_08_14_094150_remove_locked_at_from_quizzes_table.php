<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up(): void
    {
        Schema::table("quizzes", function (Blueprint $table): void {
            $table->dropColumn("locked_at");
        });
    }

    public function down(): void
    {
        Schema::table("quizzes", function (Blueprint $table): void {
            $table->timestamp("locked_at")->nullable();
        });
    }
};
