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
            $table->text('objective')->nullable(); // descrição do job
            $table->text('material')->nullable(); // uso do material
            $table->text('technical')->nullable(); // informações técnicas
            $table->text('content_info')->nullable(); // mensagem e informações de conteúdo
            $table->text('creative_details')->nullable(); // conceito criativo / identidade do job
            $table->text('deliverables')->nullable(); // entregáveis
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
