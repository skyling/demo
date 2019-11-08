<?php

namespace Demo\Http\Controllers\Admin;

use Demo\Code;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Demo\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Qiniu\Config;
use Ramsey\Uuid\Uuid;

class UploadController extends Controller
{
    const TOKEN_EXPIRES = 3600;// token 有效时长
    private $bucket; // 仓库名称

    public function __construct()
    {
        $this->bucket = config('filesystems.disks.qiniu.bucket', 'alcon');
    }

    /**
     * 七牛上传参数
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadParams(Request $request)
    {
        $key = Cache::remember('admin_upload_key', Carbon::now()->addDay(1), function () {
            $key = Uuid::uuid5(Uuid::NAMESPACE_DNS, time())->toString();
            return $key;
        });
        $data = [
            'cdnPrefix' => env('APP_URL'),
            'uploadUrl' => '/admin/upload',
            'uploadToken' => $key,
            'expiresIn' => Carbon::now()->addDay(1)->getTimestamp(),
        ];
        return response()->json($data);
        if ($request->input('refresh')) {
            Cache::forget('uploadParams');
        }
        $data = Cache::remember('uploadParams', self::TOKEN_EXPIRES / 60 - 1, function () {
            $disk = Storage::disk('qiniu');
            $uploadToken = $disk->getUploadToken(null, self::TOKEN_EXPIRES);
            $adapter = $disk->getAdapter();
            $cdnPrefix = env('QINIU_DOMAIN');
            $config = new Config();
            $uploadUrl = $config->getUpHost($adapter->getAuthManager()->getAccessKey(), env('QINIU_BUCKET'));
            $expiresIn = time() + self::TOKEN_EXPIRES;
            return compact('uploadToken', 'cdnPrefix', 'uploadUrl', 'expiresIn');
        });
        return response()->json($data);
    }

    public function upload(Request $request)
    {
        $key = Cache::get('admin_upload_key', '');
        if ($request->input('token') != $key) {
            return response()->json(['msg' => 'Token Error'], Code::HTTP_FORBIDDEN);
        }
        $filename = md5(Carbon::now()->toDateTimeString()) . '_' . $request->file('file')->getClientOriginalName();
        $path = $request->file('file')->storePubliclyAs('uploads', $filename);
        return [
            'hash' => '/' . $path,
            'key' => '/' . $path,
        ];
    }
}
