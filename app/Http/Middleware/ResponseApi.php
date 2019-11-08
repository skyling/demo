<?php

namespace Demo\Http\Middleware;

use Closure;
use Demo\Code;
use Demo\Util\Helper;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ResponseApi
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!$request->hasHeader(Helper::X_API_UUID)) {
            return response(['Error'], Code::HEADER_MISS);
        }
        $response = $next($request);
        // if ($request->expectsJson()) {
        //     $code = $response->getStatusCode();
        //     $data = ['code' => $code];
        //     $json = $response->getContent();
        //     $jsonData = @json_decode($json, true);
        //     $json = $jsonData ?: $json;
        //     if ($json) {
        //         if ($code === Response::HTTP_OK) {
        //             $data['data'] = $json;
        //         } else {
        //             $data['msg']  = is_array($json) ? array_get($json, 'message', array_get($json, 'error')) : (is_string($json) ? $json : '');
        //             $data['error'] = $json;
        //         }
        //     }
        //     return response()->json($data);
        // }
        return $response;
    }
}
