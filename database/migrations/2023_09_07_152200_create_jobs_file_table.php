<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\JobFile;

class CreateJobsFileTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jobs_file', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_id')
                ->constrained('jobs')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->foreignId('create_user_id')
                ->constrained('users')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->string('title', 60);
            $table->text('url');
            $table->enum('type', array_keys(JobFile::JOB_FILE_TYPES));
            $table->enum('job_section', array_keys(JobFile::JOB_SECTIONS))->nullable(); // "sub-tipo"
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
            ALTER TABLE jobs_file DROP FOREIGN KEY jobs_file_job_id_foreign;
            ALTER TABLE jobs_file DROP FOREIGN KEY jobs_file_create_user_id_foreign;
        ");
        Schema::dropIfExists('jobs_file');
    }
}
