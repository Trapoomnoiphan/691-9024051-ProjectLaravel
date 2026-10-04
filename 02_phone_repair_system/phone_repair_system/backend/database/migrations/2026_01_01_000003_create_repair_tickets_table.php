<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Table 3: repair_tickets (Exactly 8 fields)
     * id, tracking_code, user_id, repair_type_id, device_model, issue_description, status, created_at
     */
    public function up(): void
    {
        Schema::create('repair_tickets', function (Blueprint $table) {
            $table->id();
            $table->string('tracking_code')->unique();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('repair_type_id')->constrained('repair_types')->cascadeOnDelete();
            $table->string('device_model');
            $table->string('issue_description');
            $table->enum('status', ['received', 'repairing', 'completed', 'cancelled'])->default('received');
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('repair_tickets');
    }
};
