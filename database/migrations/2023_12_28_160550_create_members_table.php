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
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->bigInteger('user_id');
            $table->bigInteger('firm_id')->nullable();
            $table->char('work_email',100);
            $table->char('barnum',10)->nullable();
            $table->char('status',10)->default("PENDING");
            $table->char('role',10)->default("USER");
            $table->dateTime('optin')->nullable();
            $table->dateTime('unsubscribe')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};
