<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Table 4: repair_status_logs (Exactly 6 fields)
     * id, ticket_id, status, note, cost, created_at
     */
    public function up(): void
    {
        Schema::create('repair_status_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained('repair_tickets')->cascadeOnDelete();
            $table->string('status');
            $table->string('note')->nullable();
            $table->decimal('cost', 10, 2)->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('repair_status_logs');
    }
};
