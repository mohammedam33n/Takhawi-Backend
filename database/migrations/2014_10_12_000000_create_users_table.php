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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('address')->nullable();
            $table->string('gender')->nullable();
            $table->string('password')->nullable();
            $table->string('mobile')->nullable();
            $table->string('National_id')->nullable();
            $table->string('drive_image')->nullable();
            $table->string('car_image')->nullable();
            $table->string('car_id')->nullable();
            $table->timestamp('image')->nullable()->comment('وثيقه العمل الحر');
            $table->enum('type',[0,1])->default(0);
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
