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
        Schema::table('claims', function (Blueprint $table) {
            $table->text('process_remarks')->nullable()->after('processed_at');
            $table->unsignedBigInteger('paid_by')->nullable()->after('process_remarks');

            $table->foreign('paid_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('claims', function (Blueprint $table) {
            $table->dropForeign(['paid_by']);
            $table->dropColumn(['process_remarks', 'paid_by']);
        });
    }
};
