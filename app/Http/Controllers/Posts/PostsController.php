<?php

namespace App\Http\Controllers\Posts;

use App\Http\Controllers\Controller;
use App\Post;
use Illuminate\Support\Facades\Auth;

class PostsController extends Controller
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

    public function viewed(Post $item)
    {
        $item->viewed = true;
        $item->save();

        return response()->json([
            'success' => true,
            'item' => $item,
        ]);

    }

    public function delete(Post $item)
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
        return Post::with('source')
            ->where('user_id', Auth::id())
            ->orderBy('id', 'DESC')
            ->get();
    }
}
