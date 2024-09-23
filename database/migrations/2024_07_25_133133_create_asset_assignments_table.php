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
        Schema::create('asset_assignments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('asset_type_id');
            $table->unsignedBigInteger('asset_id');
            $table->unsignedBigInteger('user_id');
            $table->date('assign_date');
            $table->date('return_date')->nullable();
            $table->integer('returned')->nullable();
            $table->integer('damaged')->nullable();
            $table->string('cost_of_damage')->nullable();
            $table->timestamps();

            $table->foreign('asset_type_id')->references('id')->on('asset_types');
            $table->foreign('asset_id')->references('id')->on('assets');
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
        Schema::dropIfExists('asset_assignments');
    }
};
