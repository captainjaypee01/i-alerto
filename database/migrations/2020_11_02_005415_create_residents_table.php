<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateResidentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('residents', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('barangay_id')->nullable();
            $table->string('first_name')->nullable();
            $table->string('middle_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('email')->unique();
            $table->string('contact_number')->unique();
            $table->date('birthdate')->nullable();
            $table->text('province')->nullable();
            $table->text('city')->nullable();
            $table->text('barangay')->nullable();
            $table->text('detailed_address')->nullable();
            $table->text('health_concern')->nullable();
            $table->boolean('pwd')->default(false)->nullable();
            $table->boolean('senior_citizen')->default(false)->nullable();
            $table->text('fingerprint')->nullable();

            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('residents');
    }
}
