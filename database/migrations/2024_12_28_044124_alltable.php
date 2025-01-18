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
        // Table tour_guides
        Schema::create('tour_guides', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('is_active')->default(true); // flag aktif
            $table->unsignedBigInteger('create_by');
            $table->unsignedBigInteger('update_by')->nullable();
            $table->timestamps();

            $table->foreign('create_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('update_by')->references('id')->on('users')->onDelete('cascade');
        });

        // Table buildings
        Schema::create('buildings', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('is_active')->default(true); // flag aktif
            $table->unsignedBigInteger('create_by');
            $table->unsignedBigInteger('update_by')->nullable();
            $table->timestamps();

            $table->foreign('create_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('update_by')->references('id')->on('users')->onDelete('cascade');
        });

        // Table building_schedules
        Schema::create('building_schedules', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_number')->unique();
            $table->unsignedBigInteger('building_id')->nullable();
            $table->date('tanggal');
            $table->time('start_time');
            $table->time('end_time');
            $table->boolean('is_available')->default(true); // flag available
            $table->boolean('is_booked')->default(false); // flag booked
            $table->date('booked_date')->nullable();
            $table->unsignedBigInteger('humas_id')->nullable();
            $table->unsignedBigInteger('create_by');
            $table->unsignedBigInteger('update_by')->nullable();
            $table->timestamps();

            $table->foreign('building_id')->references('id')->on('buildings')->onDelete('cascade');
            $table->foreign('humas_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('create_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('update_by')->references('id')->on('users')->onDelete('cascade');
        });

        // Table visit_reservations
        Schema::create('visit_reservations', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_number')->unique();
            $table->unsignedBigInteger('building_schedule_id');
            $table->string('visitor_company')->nullable();
            $table->text('visitor_address')->nullable();
            $table->string('visitor_purphose')->nullable();
            $table->string('visitor_contact')->nullable();
            $table->string('visitor_person')->nullable();
            $table->text('visitor_note')->nullable();
            $table->boolean('is_available')->default(true); // flag available
            $table->boolean('is_booked')->default(false); // flag booked
            $table->boolean('tour_guide_requested')->default(false); // flag booked
            $table->boolean('tour_guide_assign')->default(false); // flag booked
            $table->boolean('is_confirm')->default(false); // flag booked
            $table->date('booked_date')->nullable();
            $table->date('tour_guide_req_date')->nullable();
            $table->date('tour_guide_assign_date')->nullable();
            $table->date('confirm_date')->nullable();
            $table->unsignedBigInteger('humas_id')->nullable();
            $table->unsignedBigInteger('visitor_id')->nullable();
            $table->unsignedBigInteger('koordinator_id')->nullable();
            $table->unsignedBigInteger('tour_guide_id')->nullable();
            $table->unsignedBigInteger('create_by');
            $table->unsignedBigInteger('update_by')->nullable();
            $table->timestamps();

            $table->foreign('building_schedule_id')->references('id')->on('building_schedules')->onDelete('cascade');
            $table->foreign('humas_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('visitor_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('koordinator_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('tour_guide_id')->references('id')->on('tour_guides')->onDelete('cascade');
            $table->foreign('create_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('update_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('visit_reservations');
        Schema::dropIfExists('building_schedules');
        Schema::dropIfExists('buildings');
        Schema::dropIfExists('tour_guides');
    }

};
