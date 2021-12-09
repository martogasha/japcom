<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class Billing extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'billing';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Billing clients';

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
     * @return int
     */
    public function handle()
    {
        return 0;
    }
}
