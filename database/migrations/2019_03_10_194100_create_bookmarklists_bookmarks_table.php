<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBookmarklistsBookmarksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bookmarklists_bookmarks', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('bookmark_id');
            $table->foreign('bookmark_id')->references('id')->on('bookmarks')->onUpdate('cascade')->onDelete('cascade');
            $table->unsignedInteger('bookmarklist_id');
            $table->foreign('bookmarklist_id')->references('id')->on('bookmarklists')->onUpdate('cascade')->onDelete('cascade');
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
        Schema::dropIfExists('bookmarklists_bookmarks');
    }
}
