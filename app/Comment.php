<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Cog\Contracts\Love\Reactable\Models\Reactable as ReactableContract;
use Cog\Laravel\Love\Reactable\Models\Traits\Reactable;

class Comment extends Model implements ReactableContract
{
    use Reactable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'body'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'commentable_id', 'commentable_type', 'user_id', 'love_reactant_id'
    ];

    /**
     * Recursivly eager load nested comments, a N+ problem but...
     *
     * @var array
     */
    protected $with = ['comments'];

    /**
     * Get all of the commentable resources.
     */
    public function commentable()
    {
        return $this->morphTo();
    }

    /**
     * Get all a posts comments.
     */
    public function comments()
    {
        return $this->morphMany('App\Comment', 'commentable');
    }

    /**
     * Get the author of a post.
     */
    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
