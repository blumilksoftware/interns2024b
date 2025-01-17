<?php

use App\Models\UserQuiz;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('disqualifications', function (Blueprint $table) {
            $table->id();
            $table->string('reason');
            $table->foreignIdFor(UserQuiz::class)->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('disqualifications');
    }
};
