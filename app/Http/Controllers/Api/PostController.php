<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\Post;
use App\Models\History;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Http\Requests\StorePostRequest;
use Barryvdh\DomPDF\Facade\Pdf;


class PostController extends Controller
{
    public function index()
    {
        $this->authorize('posts.view');

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
                // // GLOBAL SEARCH
                ->when(request('search_global'), function($query) {
                    $query->where(function($q) {
                        $q->where('no', request('search_global'))
                            ->orWhere('division', 'like', '%'.request('search_global').'%')
                            ->orWhere('content', 'like', '%'.request('search_global').'%');
                    });
                })

                // DATE FILTER
                ->when(request('expired_days'), function($query) {
                    $query->whereDate('inspection_date', '<', Carbon::now()->subDays(request('expired_days')));
                })

                // YEAR FILTER - uvek se primenjuje, default je tekuca godina
                ->whereYear('inspection_date', request('year', now()->year))
                
                ->orderBy($orderColumn, $orderDirection)
                ->paginate(10);

        return PostResource::collection($posts);
    }

    public function store(StorePostRequest $request)
    {
        $this->authorize('posts.create');

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

    public function postsPdf(Request $request)
    {
        $this->authorize('posts.view');

        $orderColumn = $request->get('order_column', 'inspection_date');
        if (!in_array($orderColumn, ['no', 'inspection_date'])) {
            $orderColumn = 'inspection_date';
        }

        $orderDirection = $request->get('order_direction', 'desc');
        if (!in_array($orderDirection, ['asc', 'desc'])) {
            $orderDirection = 'desc';
        }

        // Uzmi post ID-eve koji odgovaraju filteru
        $postIds = DB::table('posts')
            ->select('posts.id')
            ->leftJoin('histories', function ($join) {
                $join->on('posts.id', '=', 'histories.post_id')
                    ->whereRaw('histories.id = (SELECT id FROM histories WHERE histories.post_id = posts.id ORDER BY post_id ASC, inspection_date DESC LIMIT 1)');
            })
            ->when($request->get('category'), function ($query) use ($request) {
                $query->where('division', 'like', '%' . $request->get('category') . '%');
            })
            ->when($request->get('search_global'), function ($query) use ($request) {
                $query->where(function ($q) use ($request) {
                    $q->where('no', $request->get('search_global'))
                        ->orWhere('division', 'like', '%' . $request->get('search_global') . '%')
                        ->orWhere('content', 'like', '%' . $request->get('search_global') . '%');
                });
            })
            ->when($request->get('expired_days'), function ($query) use ($request) {
                $query->whereDate('inspection_date', '<', Carbon::now()->subDays($request->get('expired_days')));
            })
            ->orderBy($orderColumn, $orderDirection)
            ->pluck('posts.id');

        // Učitaj Post modele sa svim historijama
        $posts = Post::with(['histories' => function ($q) {
                $q->orderBy('inspection_date', 'desc');
            }])
            ->whereIn('id', $postIds)
            ->orderBy('division')
            ->orderBy('no')
            ->get();

        $divisionA = $posts->pluck('division')->unique()->sort()->values()->get(0, '');
        $divisionB = $posts->pluck('division')->unique()->sort()->values()->get(1, '');

        $postsA = $posts->filter(fn($p) => $p->division === $divisionA)->values();
        $postsB = $posts->filter(fn($p) => $p->division === $divisionB)->values();

        $pdf = Pdf::loadView('pdf.posts_report', [
            'postsA'      => $postsA,
            'postsB'      => $postsB,
            'divisionA'   => $divisionA,
            'divisionB'   => $divisionB,
            'generatedAt' => now()->format('Y-m-d H:i'),
        ])->setPaper('a4', 'portrait');

        return $pdf->stream('posts-' . now()->format('Ymd-His') . '.pdf');
    }

    /**
     * PDF pune istorije pregleda - sve histories grupisane po košnici (post)
     */
    public function hiveHistoryPdf()
    {
        $this->authorize('posts.view');

        $posts = Post::with(['histories' => function ($q) {
            $q->orderBy('inspection_date', 'desc');
        }])->orderBy('division')->orderBy('no')->get();

        $totalInspections = History::count();
        $firstInspection  = History::orderBy('inspection_date', 'asc')->first();
        $lastInspection   = History::orderBy('inspection_date', 'desc')->first();

        $pdf = Pdf::loadView('pdf.hive_history', [
            'posts'            => $posts,
            'totalInspections' => $totalInspections,
            'totalHives'       => $posts->count(),
            'firstDate'        => $firstInspection?->inspection_date,
            'lastDate'         => $lastInspection?->inspection_date,
            'generatedAt'      => now()->format('Y-m-d H:i'),
        ])->setPaper('a4', 'portrait');

        return $pdf->stream('hive-history-' . now()->format('Ymd-His') . '.pdf');
    }

}
