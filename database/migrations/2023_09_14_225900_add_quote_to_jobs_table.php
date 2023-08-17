<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddQuoteToJobsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('jobs', function (Blueprint $table) {
            $table->foreignId('quote_id')
                ->unique()
                ->nullable()
                ->constrained('quotes')
                ->onUpdate('cascade')
                ->onDelete('restrict');
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
            ALTER TABLE jobs DROP FOREIGN KEY jobs_quote_id_foreign;
        ");
        Schema::table('jobs', function (Blueprint $table) {
            $table->dropColumn('quote_id');
        });
    }
}
