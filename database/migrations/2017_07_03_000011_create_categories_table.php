<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     * @table categories
     *
     * @return void
     */
    public function up()
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('parent_id')->nullable()->default('0');
            $table->string('name', 100)->nullable()->default(null);
            $table->string('slug', 100)->nullable()->default(null);
            $table->integer('lft')->default('0');
            $table->integer('rgt')->default('0');
            $table->integer('depth')->default('0');
            $table->string('url', 80)->nullable();
            $table->unique(["slug"], 'unique_categories');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
     public function down()
     {
       Schema::dropIfExists('categories');
     }
}
