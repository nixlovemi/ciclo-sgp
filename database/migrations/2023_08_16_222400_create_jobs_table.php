<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\Job;

class CreateJobsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->string('uid', 30)->unique(); // PIT - sequence
            $table->foreignId('create_user_id')
                ->constrained('users')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->foreignId('client_id')
                ->constrained('clients')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->string('title', 60);
            $table->string('responsible', 60)->nullable();
            $table->date('due_date');
            $table->enum('status', array_keys(Job::JOB_STATUSES));
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
            ALTER TABLE jobs DROP FOREIGN KEY jobs_client_id_foreign;
            ALTER TABLE jobs DROP FOREIGN KEY jobs_create_user_id_foreign;
        ");
        Schema::dropIfExists('jobs');
    }
}
