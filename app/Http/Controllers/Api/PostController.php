<?php

namespace App\Http\Controllers\Api;

use App\Models\Post;
use App\Models\History;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Http\Requests\StorePostRequest;
use Illuminate\Database\Eloquent\Builder;

class PostController extends Controller
{
    public function index()  
    {
        $orderColumn = request('order_column','inspection_date');
        if (!in_array($orderColumn, ['no','inspection_date'])) {
            $orderColumn = 'inspection_date';
        }

        $orderDirection = request('order_direction', 'desc');
        if (!in_array($orderDirection, ['asc', 'desc'])) {
            $orderDirection = 'desc';
        }

        $posts = DB::table('posts')
                ->select('posts.*', 'histories.*','histories.id as history_id')
                ->leftJoin('histories', function ($join) {
                    $join->on('posts.id', '=', 'histories.post_id')
                        ->whereRaw('histories.id = (SELECT id FROM histories WHERE histories.post_id = posts.id ORDER BY post_id ASC, inspection_date DESC LIMIT 1)');
                        // ->orderBy('post_id','asc')
                        // ->orderByDesc('inspection_date');
                    })
                ->when(request('category'), function($query) {
                    $query->where('division','like', '%'.request('category').'%');
                })
                ->orderBy($orderColumn, $orderDirection)
                ->paginate(100);

        return PostResource::collection($posts);
    }

    public function store(StorePostRequest $request)
    {
        $validatedData = $request->validated();

        $post = Post::firstOrCreate(
            [
                'no' => $validatedData['no'],
                'division' => $validatedData['division'],
            ]
            );

        if ($post->wasRecentlyCreated || !$post->wasRecentlyCreated) {
            $history = History::create([
                'post_id' => $post->id,
                'content' => $validatedData['content'],
                'inspection_date' => $validatedData['inspection_date'],
            ]);
        }

        return new PostResource($post);
    }
}