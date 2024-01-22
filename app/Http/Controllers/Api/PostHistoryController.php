<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Resources\PostHistoryResource;

class PostHistoryController extends Controller
{
    public function index()
    {
        $postHistory = DB::table('posts')
            ->select('posts.*', 'histories.*','histories.id as history_id')
            ->leftJoin('histories', function ($join) {
                $join->on('posts.id', '=', 'histories.post_id')
                    ->orderBy('post_id','asc')
                    ->orderByDesc('inspection_date');
            })->get();

        return PostHistoryResource::collection($postHistory);
    }
}
