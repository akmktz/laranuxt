<?php

namespace App\Http\Controllers\Posts;

use App\Post;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class PostsController extends Controller
{
    public function index()
    {
        $list = Post::with('source')
            ->where('user_id', Auth::id())
            ->orderBy('id', 'DESC')
            ->get();

        return response()->json([
            'success' => true,
            'list' => $list,
        ]);

    }
}
