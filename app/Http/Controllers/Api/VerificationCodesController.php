<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Requests\Api\VerificationCodeRequest;
use Illuminate\Support\Facades\Redis;

class VerificationCodesController extends Controller
{
    public function store(VerificationCodeRequest $request)
    {
        $captchaData = \Cache::get($request->captcha_key);
        if (!$captchaData) {
            return $this->response->error('图片验证码已失效', 422);
        }

        if (!hash_equals($captchaData['code'], $request->captcha_code)) {
            // 验证错误就清除缓存
            \Cache::forget($request->captcha_key);
            return $this->response->errorUnauthorized('验证码错误');
        }
        $phone = $captchaData['phone'];
        $expiredAt = now()->addMinutes(5);
        $result = send_sms($phone);//发送短信验证码
        \Cache::forget($request->captcha_key);// 清除图片验证码缓存
        if ($result)
        {
            return $this->response->array([
                'key' => 'web_sms_code_' . $phone,
                'expired_at' => $expiredAt->toDateTimeString(),
            ])->setStatusCode(201);
        }
        else {
            return $this->response->error('短信发送异常', 422);
        }
    }
}
