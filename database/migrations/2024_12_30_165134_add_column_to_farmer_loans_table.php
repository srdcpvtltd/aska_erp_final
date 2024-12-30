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
        Schema::table('farmer_loans', function (Blueprint $table) {
            $table->integer('farming_payment_id')->nullable()->default('0')->after('invoice_generate_status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('farmer_loans', function (Blueprint $table) {
            $table->integer('farming_payment_id')->nullable()->default('0')->after('invoice_generate_status');
        });
    }
};
