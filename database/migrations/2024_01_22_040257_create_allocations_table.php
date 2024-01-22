<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAllocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('allocations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('source_id')->constrained('receipts', 'id')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('allocatee_id')->constrained('invoices', 'id')->onUpdate('cascade')->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->date('date');
            $table->string('source_type')->default('Receipt');
            $table->enum('allocatee_type', ['Invoice', 'Bill', 'CreditNote', 'DebitNote'])->default('Invoice');
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
        Schema::dropIfExists('allocations');
    }
}
