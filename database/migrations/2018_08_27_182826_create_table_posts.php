<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTablePosts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->increments('id');
            $table->enum('type', ['TWITTER']);
            $table->addColumn('integer', 'user_id', ['unsigned' => true, 'length' => 11])->index();
            $table->addColumn('integer', 'source_id', ['unsigned' => true, 'length' => 11]);
            $table->bigInteger('original_id', false, true);
            $table->dateTime('original_date');
            $table->text('title');
            $table->text('body');
            $table->text('url');
            $table->boolean('viewed')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['user_id', 'type', 'original_id']);
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('source_id')->references('id')->on('posts_sources')->onUpdate('cascade')->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('posts');
    }
}
