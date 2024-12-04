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
        Schema::table('farming_details', function (Blueprint $table) {
            $table->string('mode_of_transport')->default('NULL')->after('total_planting_area');
            $table->string('reserve_seed')->default('NULL')->after('mode_of_transport');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('farming_details', function (Blueprint $table) {
            $table->string('mode_of_transport')->default('NULL')->after('total_planting_area');
            $table->string('reserve_seed')->default('NULL')->after('mode_of_transport');
        });
    }
};
