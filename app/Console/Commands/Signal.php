<?php

namespace App\Console\Commands;

use App\Jobs\SignalUpdated;
use App\User;
use Illuminate\Console\Command;

class Signal extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'signal:updated';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'signal:updated';

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
        $users = User::get();
        foreach ($users as $user) {
            dispatch(new SignalUpdated($user->id));
        }
    }
}
