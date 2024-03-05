<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class JobAddColumnUserResponsible extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('jobs', function (Blueprint $table) {
            $table->foreignId('user_responsible_id')
                ->nullable()
                ->after('responsible')
                ->constrained('users')
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
            ALTER TABLE jobs DROP FOREIGN KEY jobs_user_responsible_id_foreign;
        ");
        Schema::table('jobs', function (Blueprint $table) {
            $table->dropColumn('user_responsible_id');
        });
    }
}
