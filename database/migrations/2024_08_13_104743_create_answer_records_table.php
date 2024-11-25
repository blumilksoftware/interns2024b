<?php

declare(strict_types=1);

use App\Models\Answer;
use App\Models\Question;
use App\Models\UserQuiz;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up(): void
    {
        Schema::create("user_questions", function (Blueprint $table): void {
            $table->id();
            $table->foreignIdFor(UserQuiz::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Question::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Answer::class)->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists("user_questions");
    }
};
