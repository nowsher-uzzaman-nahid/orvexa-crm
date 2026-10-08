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
        Schema::create('deals', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('customer_id')
                ->constrained()
                ->restrictOnDelete();

            $table->string('title');

            $table->text('description')->nullable();

            $table->decimal('value', 15, 2)->nullable();

            $table->string('currency', 3)->default('USD');

            $table->foreignId('deal_stage_id')
                ->constrained('deal_stages')
                ->restrictOnDelete();

            $table->foreignId('assigned_to')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->date('expected_close_date')->nullable();

            $table->string('status')->default('open');

            $table->timestamp('closed_at')->nullable();

            $table->text('lost_reason')->nullable();

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->softDeletes();

            $table->index('customer_id');
            $table->index('deal_stage_id');
            $table->index('assigned_to');
            $table->index('status');
            $table->index('expected_close_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deals');
    }
};
