<?php

namespace App\Jobs;

use App\Post;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ParseTwitterSaveTweet implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $source;
    private $id;
    private $url;
    private $title;
    private $date;

    /**
     * Create a new job instance.
     * @param array $source
     * @param int $id
     * @param string $url
     * @param string $title
     * @param Carbon $date
     */
    public function __construct($source, $id, $url, $title, $date)
    {
        $this->source = $source;
        $this->id = $id;
        $this->url = $url;
        $this->title = $title;
        $this->date = $date;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $user = str_replace('@', '', array_get($this->source, 'account_name'));
        $url = 'https://twitter.com/' . $user . '/status/' . $this->id;
        $data = json_decode(file_get_contents('https://publish.twitter.com/oembed?url='. urlencode($url)));

        $post = new Post();
        $post->type = array_get($this->source, 'type');
        $post->user_id = array_get($this->source, 'user_id');
        $post->source_id = array_get($this->source, 'id');
        $post->original_id = $this->id;
        $post->original_date = $this->date->toDateTimeString();
        $post->title = $this->title;
        $post->body = $data->html;
        $post->url = $this->url;
        $post->save();
    }
}
