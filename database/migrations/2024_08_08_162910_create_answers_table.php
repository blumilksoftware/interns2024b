<?php

declare(strict_types=1);

use App\Models\Question;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up(): void
    {
        Schema::create("answers", function (Blueprint $table): void {
            $table->id();
            $table->timestamps();
            $table->text("text");
            $table->foreignIdFor(Question::class)->constrained()->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists("answers");
    }
};
