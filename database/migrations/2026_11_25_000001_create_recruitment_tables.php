<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vacancies', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->string('contract', 20);
            $table->boolean('paused')->default(false);
            $table->timestamps();
        });
        Schema::create('candidates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamps();
        });
        Schema::create('candidate_vacancy', function (Blueprint $table) {
            $table->foreignId('candidate_id')->constrained()->cascadeOnDelete();
            $table->foreignId('vacancy_id')->constrained()->cascadeOnDelete();
            $table->primary(['candidate_id', 'vacancy_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('candidate_vacancy');
        Schema::dropIfExists('candidates');
        Schema::dropIfExists('vacancies');
    }
};
