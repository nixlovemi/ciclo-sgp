<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('create_user_id')
                ->constrained('users')
                ->onUpdate('cascade')
                ->onDelete('restrict');

            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone', 35)->nullable();

            $table->string('business_name')->nullable(); // razão social
            $table->string('business_id', 50)->unique()->nullable(); // cpf, cnpj, etc
            $table->string('business_email')->nullable();
            $table->string('business_phone', 35)->nullable();

            $table->string('postal_code', 20)->nullable();
            $table->string('street')->nullable();
            $table->string('street_2')->nullable();
            $table->string('city')->nullable();
            $table->string('province')->nullable(); // estado
            $table->string('country')->nullable();

            $table->text('notes')->nullable(); // observação
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
            ALTER TABLE clients DROP FOREIGN KEY clients_create_user_id_foreign;
        ");
        Schema::dropIfExists('clients');
    }
}
