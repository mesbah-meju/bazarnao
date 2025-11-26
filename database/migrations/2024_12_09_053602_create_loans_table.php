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
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->string('borrower_name');
            $table->decimal('loan_amount', 15, 2);
            $table->integer('loan_term'); // in years
            $table->decimal('interest_rate', 5, 2);
            $table->date('start_date');
            $table->enum('status', ['Pending', 'Processing', 'Completed'])->default('Pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};
