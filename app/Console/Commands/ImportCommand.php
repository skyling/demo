<?php

namespace Demo\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use League\Flysystem\FileNotFoundException;
use Demo\Model\Import;
use Demo\Services\ImportService;

class ImportCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:task';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '导入任务执行';

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
        $key = 'import_lock';
//        Cache::forget($key);
        if (Cache::get($key)) {
            return false;
        }
        Cache::put($key, 1, 20);
        try {
            Import::query()->orderBy('id', 'asc')
                ->where('status', Import::STATUS_WAIT)
                ->chunk(10, function ($data) {
                    foreach ($data as $import) {
                        $import->status = Import::STATUS_PENDING;
                        $import->save();
                        try {
                            if (!$import->filepath) {
                                throw new FileNotFoundException('文件不存在');
                            }
                            $ret = [];
                            $start = Carbon::now();
                            switch ($import->type) {
                                case Import::TYPE_ORDER:
                                    $ret = ImportService::orderImport($import->filepath);
                                    break;
                                case Import::TYPE_LOGISTICS:
                                    $ret = ImportService::logisticsImport($import->filepath);
                                    break;
                                case Import::TYPE_PURCHASE:
                                    $ret = ImportService::purchaseImport($import->filepath);
                                    break;
                            }
                            $import->duration = Carbon::now()->diffInSeconds($start);
                            $import->result = $ret;
                            $import->status = Import::STATUS_SUCCESS;
                        } catch (\Exception $e) {
                            Log::error($e);
                            $import->result = ['msg' => $e->getMessage(), 'detail' => $e];
                            $import->status = Import::STATUS_ERROR;
                        }
                        $import->save();
                    }
                });
        } catch (\Exception $e) {
            Log::error($e);
        }
        Cache::forget($key);
    }
}
