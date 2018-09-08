<?php

namespace App\Console\Commands;

use App\Jobs\ParseTwitter;
use App\Post;
use App\PostsSource;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ParsePosts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'parse:posts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Parse posts';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $minDate = Carbon::now()->subHours(24);

        $sources = PostsSource::where('enabled', true)->get();
        foreach ($sources as $source) {
            dispatch(new ParseTwitter($minDate, $source));
        }

        // Delete old unpublished news
        $cleanDate = Carbon::now()->subYear(1);
        Post::where('viewed', true)->where('original_date', '<', $cleanDate)->delete();
    }
}
