<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class ServiceItemsDescriptionSize extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("
            ALTER TABLE service_items MODIFY COLUMN description varchar(160) NOT NULL;
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("
            ALTER TABLE service_items MODIFY COLUMN description varchar(80) NOT NULL;
        ");
    }
}
