<?php

use App\Models\Quote;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateQuotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quotes', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->foreignId('create_user_id')
                ->constrained('users')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->foreignId('client_id')
                ->constrained('clients')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->integer('validity_days', false, true);
            $table->enum('payment_type', Quote::PAYMENT_TYPES);
            $table->text('payment_type_memo')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('active')->default(true);
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
            ALTER TABLE quotes DROP FOREIGN KEY quotes_client_id_foreign;
            ALTER TABLE quotes DROP FOREIGN KEY quotes_create_user_id_foreign;
        ");
        Schema::dropIfExists('quotes');
    }
}
