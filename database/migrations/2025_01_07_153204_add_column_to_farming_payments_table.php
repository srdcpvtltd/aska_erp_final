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
        Schema::table('farming_payments', function (Blueprint $table) {
            $table->string('invoice_no')->nullable()->after('g_code');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('farming_payments', function (Blueprint $table) {
            $table->dropColumn('invoice_no');
        });
    }
};
