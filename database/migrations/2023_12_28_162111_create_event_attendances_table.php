<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('event_attendances', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->dateTime('start');
            $table->integer('duration')->comment('minutes');
            $table->dateTime('doors_open')->comment('Must be before start.');
            $table->text('location');
            $table->text('description');
            $table->char('title',250);
            $table->boolean('continuing_education_available')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_attendances');
    }
};
