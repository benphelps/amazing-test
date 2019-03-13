<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Cog\Contracts\Love\Reactable\Models\Reactable as ReactableContract;
use Cog\Laravel\Love\Reactable\Models\Traits\Reactable;

class Post extends Model implements ReactableContract
{
    use Reactable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title', 'body'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'user_id', 'love_reactant_id'
    ];

    /**
     * Eager load comments and reactions
     */
    protected $with = ['comments', 'loveReactant.reactionCounters'];

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
