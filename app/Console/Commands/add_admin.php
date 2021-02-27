<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
class add_admin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'add_admin {username} {email} {password}';

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
        DB::table('users')->insert([
        // User::insert([
            'username' => $this->argument('username'),
            'email' => $this->argument("email"),
            'display_name' => 'Tao la admin',
            // 'email' => Str::random(10).'@gmail.com',
            'password' => Hash::make($this->argument('password')),
            'role_id' => '1' //Admin
        ]);
    }
}
