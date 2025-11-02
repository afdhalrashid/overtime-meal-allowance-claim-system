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
        Schema::create('claims', function (Blueprint $table) {
            $table->id();
            $table->string('claim_number')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('duty_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->enum('work_type', ['in_office', 'out_of_office']);
            $table->boolean('has_overtime')->default(false);
            $table->boolean('has_meal_allowance')->default(false);
            $table->decimal('overtime_hours', 5, 2)->default(0);
            $table->decimal('meal_allowance_amount', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2)->default(0);

            // Travel details for out-of-office work
            $table->time('travel_start_time')->nullable();
            $table->time('travel_end_time')->nullable();
            $table->string('travel_origin')->nullable();
            $table->string('travel_destination')->nullable();
            $table->text('travel_purpose')->nullable();
            $table->decimal('travel_hours', 5, 2)->default(0);

            $table->enum('status', ['draft', 'pending_approval', 'approved', 'rejected', 'processed', 'paid'])->default('draft');
            $table->text('remarks')->nullable();
            $table->text('approval_remarks')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('processed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('processed_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'duty_date']);
            $table->index(['status', 'duty_date']);
            $table->index('claim_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('claims');
    }
};
