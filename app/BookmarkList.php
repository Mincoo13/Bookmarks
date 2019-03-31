<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BookmarkList extends Model
{
    public $table = "bookmarklists";

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function bookmark()
    {
        return $this->belongsToMany(Bookmark::class);
    }
}
