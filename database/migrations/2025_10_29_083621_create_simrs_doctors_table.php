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
        Schema::create('simrs_doctors', function (Blueprint $table) {
            $table->string('doctor_id', 15)->primary();
            $table->string('doctor_name', 255);
            $table->enum('doctor_gender', ['M', 'F']);
            $table->string('doctor_phone_number', 20)->nullable();
            $table->string('doctor_address', 255)->nullable();
            $table->string('doctor_email', 255)->nullable();
            $table->text('doctor_bio')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->dateTime('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('simrs_doctors');
    }
};
