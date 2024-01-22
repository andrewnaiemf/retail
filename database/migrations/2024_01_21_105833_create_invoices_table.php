<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contact_id')->constrained('users', 'id')->onUpdate('cascade')->onDelete('cascade');
            $table->string('reference')->nullable();
            $table->text('description');
            $table->date('issue_date');
            $table->date('due_date');
            $table->enum('status', ['Approved', 'Pending', 'Cancelled', 'Paid', 'Partially Paid', 'Draft']);
            $table->decimal('due_amount', 10, 2);
            $table->decimal('paid_amount', 10, 2);
            $table->decimal('total', 10, 2);
            $table->longText('notes')->nullable();
            $table->text('terms_conditions')->nullable();
            $table->text('qrcode_string')->nullable();
            $table->string('payment_method')->nullable();
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
        Schema::dropIfExists('invoices');
    }
}
