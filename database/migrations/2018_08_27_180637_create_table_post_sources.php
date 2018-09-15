<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTablePostSources extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('posts_sources', function (Blueprint $table) {
            $table->increments('id');
            $table->boolean('enabled')->default(true);
            $table->enum('type', ['TWITTER']);
            $table->addColumn('integer', 'user_id', ['unsigned' => true, 'length' => 11])->index();
            $table->string('name');
            $table->string('account_name')->comment('User name on source resource');
            $table->boolean('filter_type')->default(true)->comment('true - whitelist, false - blacklist');
            $table->string('filter_words')->nullable();
            $table->dateTime('synchronized_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('posts_sources');
    }
}
