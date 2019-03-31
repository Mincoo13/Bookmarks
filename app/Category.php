<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function bookmark()
    {
        return $this->hasMany(Bookmark::class);
    }

    protected $fillable = [
        'name', 'user_id',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'remember_token',
    ];
}
