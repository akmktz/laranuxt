<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SignalUpdated implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $userId;

    /**
     * Create a new job instance.
     *
     * @param int $userId
     */
    public function __construct(int $userId)
    {
        $this->userId = $userId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if (!$this->userId
            || env('SIGNAL_SERVER_ENABLED', false) !== true
            || !env('SIGNAL_SERVER_UDP_PORT', false))
        {
            return;
        }

        $errno = null;
        $errstr = null;

        $udpPort = env('SIGNAL_SERVER_UDP_PORT');

        $fp = stream_socket_client("udp://127.0.0.1:" . $udpPort, $errno, $errstr);
        if ($errno) {
            echo $errstr;
            return;
        }

        fwrite($fp, "UPDATED_USER_ID:" . $this->userId);
    }
}
