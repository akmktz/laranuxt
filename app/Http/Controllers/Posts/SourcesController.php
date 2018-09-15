<?php

namespace App\Http\Controllers\Posts;

use App\Http\Controllers\Controller;
use App\Http\Requests\PostSourceRequest;
use App\Http\Requests\SetStatusRequest;
use App\Post;
use App\PostsSource;
use Illuminate\Support\Facades\Auth;

class SourcesController extends Controller
{
    public function __construct()
    {
        $this->middleware(['owner']);
    }

    public function index()
    {
        $list = $this->getList();

        return response()->json([
            'success' => true,
            'list' => $list,
        ]);

    }

    public function setStatus(SetStatusRequest $request, PostsSource $item)
    {
        $item->enabled = $request->get('enabled');
        $item->save();

        return response()->json([
            'success' => true,
            'item' => $item,
        ]);

    }

    public function add(PostSourceRequest $request, PostsSource $item)
    {
        $item->fill($request->only(['type', 'name', 'account_name']));
        $item->user_id = Auth::id();
        $item->save();

        $list = $this->getList();

        return response()->json([
            'success' => true,
            'list' => $list,
        ]);
    }

    public function save(PostSourceRequest $request, PostsSource $item)
    {
        $item->fill($request->only(['type', 'name', 'account_name']));
        $item->save();

        return response()->json([
            'success' => true,
            'item' => $item,
        ]);
    }

    public function delete(PostsSource $item)
    {
        $item->delete();

        $list = $this->getList();

        return response()->json([
            'success' => true,
            'list' => $list,
        ]);

    }

    /**
     * @return Post[]|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    private function getList()
    {
        return PostsSource::where('user_id', Auth::id())
            ->orderBy('id', 'ASC')
            ->get();
    }
}
