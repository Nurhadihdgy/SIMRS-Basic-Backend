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
        Schema::create('simrs_schedules', function (Blueprint $table) {
            $table->increments('schedule_id');
            $table->string('doctor_id', 15);
            $table->string('pol_id', 10);
            $table->date('schedule_date');
            $table->time('schedule_start');
            $table->time('schedule_end');
            $table->timestamp('created_at')->useCurrent();
            $table->dateTime('updated_at')->nullable();

            // Foreign keys
            $table->foreign('doctor_id')
                ->references('doctor_id')->on('simrs_doctors')
                ->cascadeOnDelete()->cascadeOnUpdate();

            $table->foreign('pol_id')
                ->references('pol_id')->on('simrs_poliklinik')
                ->cascadeOnDelete()->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('simrs_schedules');
    }
};
