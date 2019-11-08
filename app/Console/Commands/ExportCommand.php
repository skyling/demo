<?php

namespace Demo\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Demo\Model\Admin;
use Demo\Model\Export;
use Demo\Services\ExportService;

class ExportCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'export:task';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '导出任务';

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
        $key = 'export_lock';
        Cache::forget($key);
        if (Cache::get($key)) {
            return false;
        }
        Cache::put($key, 1, 120);
        try {
            Export::query()->orderBy('id', 'asc')
                ->where('status', Export::STATUS_WAIT)
                ->chunkById(10, function ($data) {
                    foreach ($data as $export) {
                        $auth = Auth::guard(Admin::GUARD_NAME);
                        $auth->onceUsingId($export->admin_user_id);
//                        $export->status = Export::STATUS_PENDING;
                        $export->save();
                        try {
                            $path = '';
                            $start = Carbon::now();
                            switch ($export->type) {
                                case Export::TYPE_ORDER:
                                    $path = ExportService::orderExport($export->params);
                                    break;
                                case Export::TYPE_ORDER_GOODS:
                                    $path = ExportService::orderGoodsExport($export->params);
                                    break;
                                case Export::TYPE_ORDER_LOGISTICS:
                                    $path = ExportService::orderLogisticsExport($export->params);
                                    break;
                                case Export::TYPE_ORDER_REFUND:
                                    $path = ExportService::orderRefundExport($export->params);
                                    break;
                                case Export::TYPE_LOGISTICS:
                                    $path = ExportService::logisticsExport($export->params);
                                    break;
                                case Export::TYPE_PURCHASE:
                                    $path = ExportService::purchaseExport($export->params);
                                    break;
                            }
                            $export->duration = Carbon::now()->diffInSeconds($start);
                            $export->filepath = $path;
                            $export->result = ['path' => $path];
                            $export->status = Export::STATUS_SUCCESS;
                        } catch (\Exception $e) {
                            Log::error($e);
                            $export->result = $e;
                            $export->status = Export::STATUS_ERROR;
                        }
                        $export->save();
                    }
                });
        } catch (\Exception $e) {
            Log::error($e);
            dd($e->getMessage(), $e->getMessage(), $e->getFile());
        }
        Cache::forget($key);
    }
}
