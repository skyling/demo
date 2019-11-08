<?php

namespace Demo\Console\Commands;

use Carbon\Carbon;
use function foo\func;
use Illuminate\Console\Command;
use Demo\Model\OrderGoods;
use Demo\Model\OrderLogistics;
use Demo\Model\Purchase;
use Demo\Services\MatchService;

class OrderMatchCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'order:match';

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
     * @return mixed
     */
    public function handle()
    {
        MatchService::goodsMatch();
        MatchService::logisticsMatch();
    }
}
