<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateJobInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jobs_invoice', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_id')
                ->unique()
                ->constrained('jobs')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->string('invoice_number', 30)->nullable(); # número da NF
            $table->date('invoice_date')->nullable(); # data faturamento
            $table->date('due_date')->nullable(); # data vencimento
            $table->decimal('total', 12, 2, true)->nullable();
            $table->text('invoice_path')->nullable(); #upload da NF
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
        DB::statement("
            ALTER TABLE jobs_invoice DROP FOREIGN KEY jobs_invoice_job_id_foreign;
        ");
        Schema::dropIfExists('jobs_invoice');
    }
}
