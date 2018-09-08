<?php

namespace App\Jobs;

use App\Post;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Thujohn\Twitter\Facades\Twitter;

class ParseTwitter implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $minDate;
    private $source;

    /**
     * Create a new job instance.
     *
     * @param $minDate
     * @param array $source
     */
    public function __construct($minDate, $source)
    {
        $this->minDate = $minDate;
        $this->source = $source;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->source->synchronized_at = Carbon::now();
        $this->source->save();

        $data = Twitter::getUserTimeline([
            'screen_name' => array_get($this->source, 'external_name'),
            'format' => 'array',
            'count' => 10,
            'trim_user' => true,
            'exclude_replies' => true,
            'include_rts' => false,
        ]);

        $maxId = 0;
        $maxItem = Post::where('source_id', array_get($this->source, 'id'))
            ->orderBy('original_date', 'DESC')
            ->first();
        if ($maxItem) {
            $maxId = (int)$maxItem->original_id;
        }

        foreach ($data as $item) {
            $id = (int)array_get($item, 'id');
            $url = array_get($item, 'entities.urls.0.url') ?: array_get($item, 'entities.media.0.url');
            $title = array_get($item, 'text');

            if (!$id || $id <= $maxId || !$url || !$title) {
                continue;
            }

            $date = Carbon::createFromFormat('* M d H:i:s T Y', array_get($item, 'created_at'));
            if ($date < $this->minDate) {
                continue;
            }

            dispatch(new ParseTwitterSaveTweet($this->source, $id, $url, $title, $date));
        }
    }
}
