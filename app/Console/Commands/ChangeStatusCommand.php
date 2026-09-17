<?php

namespace App\Console\Commands;

use App\Models\Order;
use Illuminate\Console\Command;

class ChangeStatusCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        Order::where('id', 1)->update(['status' => 'occupied']);
        $this->info('Status changed successfully!');
    }
}
