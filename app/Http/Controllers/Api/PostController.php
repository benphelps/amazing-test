<?php

namespace App\Http\Controllers\Api;

use App\Post;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Cog\Laravel\Love\ReactionType\Models\ReactionType;
use Cog\Contracts\Love\Reaction\Exceptions\ReactionAlreadyExists;

class PostController extends Controller
{
    /**
     * Create a new PostController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['only' => ['store', 'like', 'dislike']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $posts = Post::with(['user', 'comments'])->paginate(5);
        return response()->json($posts);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()]);
        }

        try {

            $post = new Post([
                'title' => $request->title,
                'body' => $request->body
            ]);
            $reactant = $post->loveReactant()->create([
                'type' => $post->getMorphClass(),
            ]);
            $post->setAttribute('love_reactant_id', $reactant->getId());
            Auth::user()->posts()->save($post);
            return response()->json($post);
        } catch (QueryException $e) {
            return response()->json(['error' => 'There was a problem creating the post.']);
        }
    }

    public function like(Request $request, Post $post)
    {
        try {
            if ($this->reactor()->isReactedToWithType($post->getLoveReactant(), ReactionType::fromName('Dislike'))) {
                $this->reactor()->unreactTo($post->getLoveReactant(), ReactionType::fromName('Dislike'));
            }
            $this->reactor()->reactTo($post->getLoveReactant(), ReactionType::fromName('Like'));
            return response()->json($post->getLoveReactant()->getReactionCounters());
        } catch (ReactionAlreadyExists $e) {
            return response()->json(['error' => 'You have already liked this post.']);
        }
    }

    public function dislike(Request $request, Post $post)
    {
        try {
            if ($this->reactor()->isReactedToWithType($post->getLoveReactant(), ReactionType::fromName('Like'))) {
                $this->reactor()->unreactTo($post->getLoveReactant(), ReactionType::fromName('Like'));
            }
            $this->reactor()->reactTo($post->getLoveReactant(), ReactionType::fromName('Dislike'));
            return response()->json($post->getLoveReactant()->getReactionCounters());
        } catch (ReactionAlreadyExists $e) {
            return response()->json(['error' => 'You have already disliked this post.']);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function show(Post $post)
    {
        $post->load('user', 'comments');
        return response()->json($post);
    }

    public function reactor()
    {
        return Auth::user()->getLoveReacter();
    }
}
