<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ride_history', function (Blueprint $table) {
            $table->id();

            $table->foreignId('rider_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('driver_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('ride_request_id')->constrained('ride_requests')->onDelete('cascade');

            $table->string('start_location');
            $table->string('end_location');
            $table->decimal('fare', 10, 2);
            $table->integer('ride_duration'); // Duration in seconds
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ride_history');
    }
};
