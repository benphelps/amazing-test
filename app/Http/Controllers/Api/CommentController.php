<?php

namespace App\Http\Controllers\Api;

use App\Post;
use App\Comment;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Cog\Laravel\Love\ReactionType\Models\ReactionType;
use Cog\Contracts\Love\Reaction\Exceptions\ReactionAlreadyExists;

class CommentController extends Controller
{
    /**
     * Create a new CommentController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['only' => ['store', 'like', 'dislike']]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Post $post, Comment $comment = null)
    {
        $validator = Validator::make($request->all(), [
            'body' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()]);
        }

        try {
            if ($comment) {
                if (
                    $comment->commentable->user != Auth::user() ||
                    $comment->comments()->get()->isEmpty() ||
                    $comment->comments()->get()->last()->user != Auth::user()
                ) {
                    $new_comment = new Comment();
                    $new_comment->body = $request->body;
                    $new_comment->user_id = Auth::user()->id;
                    $reactant = $new_comment->loveReactant()->create([
                        'type' => $new_comment->getMorphClass(),
                    ]);
                    $new_comment->setAttribute('love_reactant_id', $reactant->getId());
                    $comment->comments()->save($new_comment);
                } else {
                    return response()->json(['error' => 'You may not create consecutive comments.']);
                }
            } else {
                if (!$post->comments()->get()->isEmpty() && $post->comments()->get()->last()->user != Auth::user()) {
                    $new_comment = new Comment();
                    $new_comment->body = $request->body;
                    $new_comment->user_id = Auth::user()->id;
                    $reactant = $new_comment->loveReactant()->create([
                        'type' => $new_comment->getMorphClass(),
                    ]);
                    $new_comment->setAttribute('love_reactant_id', $reactant->getId());
                    $post->comments()->save($new_comment);
                } else {
                    return response()->json(['error' => 'You may not create consecutive comments.']);
                }
            }
            return response()->json($new_comment);
        } catch (QueryException $e) {
            return response()->json(['error' => 'There was a problem creating the post.']);
        }
    }

    public function like(Request $request, Post $post, Comment $comment)
    {
        try {
            if ($this->reactor()->isReactedToWithType($comment->getLoveReactant(), ReactionType::fromName('Dislike'))) {
                $this->reactor()->unreactTo($comment->getLoveReactant(), ReactionType::fromName('Dislike'));
            }
            $this->reactor()->reactTo($comment->getLoveReactant(), ReactionType::fromName('Like'));
            return response()->json($comment->getLoveReactant()->getReactionCounters());
        } catch (ReactionAlreadyExists $e) {
            return response()->json(['error' => 'You have already liked this comment.']);
        }
    }

    public function dislike(Request $request, Post $post, Comment $comment)
    {
        try {
            if ($this->reactor()->isReactedToWithType($comment->getLoveReactant(), ReactionType::fromName('Like'))) {
                $this->reactor()->unreactTo($comment->getLoveReactant(), ReactionType::fromName('Like'));
            }
            $this->reactor()->reactTo($comment->getLoveReactant(), ReactionType::fromName('Dislike'));
            return response()->json($comment->getLoveReactant()->getReactionCounters());
        } catch (ReactionAlreadyExists $e) {
            return response()->json(['error' => 'You have already disliked this comment.']);
        }
    }

    public function reactor()
    {
        return Auth::user()->getLoveReacter();
    }
}
