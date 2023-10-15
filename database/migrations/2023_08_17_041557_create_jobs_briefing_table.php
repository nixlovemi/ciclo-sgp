<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateJobsBriefingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jobs_briefing', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_id')
                ->unique()
                ->constrained('jobs')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->text('objective')->nullable();
            $table->text('background')->nullable(); // histórico
            $table->text('creative_details')->nullable(); // premissas para criação
            $table->text('measurements')->nullable(); // medidas
            $table->text('notes')->nullable(); // observações
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
            ALTER TABLE jobs_briefing DROP FOREIGN KEY jobs_briefing_job_id_foreign;
        ");
        Schema::dropIfExists('jobs_briefing');
    }
}
