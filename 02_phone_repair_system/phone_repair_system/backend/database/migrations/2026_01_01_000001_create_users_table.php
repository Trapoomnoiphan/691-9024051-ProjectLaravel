<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Table 1: users (Exactly 6 fields)
     * id, name, email, password, phone, role
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('phone')->unique();
            $table->enum('role', ['admin', 'user'])->default('user');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
