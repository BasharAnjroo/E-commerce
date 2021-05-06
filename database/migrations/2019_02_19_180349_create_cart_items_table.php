<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCartItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cart_items', function (Blueprint $table) {
            $table->string('cart_id');
            $table->integer('product_id')->unsigned();

            $table->unsignedInteger('quantity');
            $table->integer('currency_id')->unsigned();
            $table->string('currency', 10);
            $table->string('value');
            $table->foreign('cart_id')
                ->references('id')->on('carts')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreign('product_id')
                ->references('id')->on('products')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreign('currency_id')
            ->references('id')->on('currencies')
            ->onDelete('cascade')
            ->onUpdate('cascade');

            $table->primary(array('cart_id', 'product_id'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cart_items');
    }
}
