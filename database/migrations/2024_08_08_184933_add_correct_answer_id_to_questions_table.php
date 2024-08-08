<?php

declare(strict_types=1);

use App\Models\Answer;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up(): void
    {
        Schema::table("questions", function (Blueprint $table): void {
            $table->foreignIdFor(Answer::class, "correct_answer_id")->nullable()->constrained("answers")->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table("questions", function (Blueprint $table): void {
            $table->dropColumn("correct_answer_id");
        });
    }
};
