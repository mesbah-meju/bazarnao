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
        Schema::create('chart_of_accounts', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->integer('head_code')->index();
            $table->string('head_name', 100);
            $table->string('p_head_name', 200);
            $table->string('p_head_code', 50)->nullable();
            $table->integer('head_level');
            $table->boolean('is_active');
            $table->boolean('is_transaction');
            $table->boolean('is_gl');
            $table->integer('is_cash_nature')->default(0);
            $table->integer('is_bank_nature')->default(0);
            $table->char('head_type', 1);
            $table->boolean('is_budget');
            $table->boolean('is_depreciation');
            $table->string('customer_id', 50)->nullable();
            $table->string('supplier_id', 50)->nullable();
            $table->string('bank_id', 100)->nullable();
            $table->string('service_id', 50)->nullable();
            $table->decimal('depreciation_rate', 18, 2);
            $table->dateTime('update_date');
            $table->integer('is_sub_type')->default(0);
            $table->integer('sub_type')->default(1);
            $table->integer('is_stock')->default(0);
            $table->integer('is_fixed_asset_sch')->default(0);
            $table->string('note_no', 20)->nullable();
            $table->string('asset_code', 20)->nullable();
            $table->string('dep_code', 20)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('chart_of_accounts');
    }
};
