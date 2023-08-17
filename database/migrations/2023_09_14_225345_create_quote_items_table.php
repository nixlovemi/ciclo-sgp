<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateQuoteItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quote_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quote_id')
                ->constrained('quotes')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->foreignId('item_id')
                ->constrained('service_items')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->decimal('quantity', 12, 2, true);
            $table->string('type', 10); // unidade (KG, HR, UN, etc)
            $table->decimal('price', 12, 2, true);
            $table->decimal('total', 12, 2, true);
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
            ALTER TABLE quote_items DROP FOREIGN KEY quote_items_quote_id_foreign;
            ALTER TABLE quote_items DROP FOREIGN KEY quote_items_item_id_foreign;
        ");
        Schema::dropIfExists('quote_items');
    }
}
