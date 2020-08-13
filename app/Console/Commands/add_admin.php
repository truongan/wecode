<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class add_admin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'add_admin username email password';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add an admin via console';

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
        //
    }
}
