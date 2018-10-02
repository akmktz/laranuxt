<?php

namespace App\Jobs;

use App\Post;
use App\PostsSource;
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

    /**
     * @var PostsSource
     */
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

        // Get max ID
        $maxId = (int)Post::withTrashed()
            ->where('source_id', $this->source->id ?? null)
            ->max('original_id');
        if ($maxId > $this->source->original_max_id) {
            $this->source->original_max_id = $maxId;
        } else {
            $maxId = (int)$this->source->original_max_id;
        }

        $this->source->save();

        // Delete soft deleted records
        Post::onlyTrashed()->where('source_id', $this->source->id ?? null)->forceDelete();

        // Get twitter timeline
        $list = Twitter::getUserTimeline([
            'screen_name' => array_get($this->source, 'account_name'),
            'format' => 'array',
            'count' => 10,
            'trim_user' => true,
            'exclude_replies' => true,
            'include_rts' => false,
        ]);

        $updated = false;

        foreach ($list as $item) {
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

            $updated = true;
        }

        if (env('SIGNAL_SERVER_ENABLED', false) && $updated) {
            dispatch(new SignalUpdated($this->source->user_id));
        }
    }
}
