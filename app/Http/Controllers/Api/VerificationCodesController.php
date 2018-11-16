<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Requests\Api\VerificationCodeRequest;
use Illuminate\Support\Facades\Redis;

class VerificationCodesController extends Controller
{
    public function store(VerificationCodeRequest $request)
    {
        $phone = $request->phone;
        $expiredAt = now()->addMinutes(5);
        $result = send_sms($phone);
        if ($result)
        {
            return $this->response->array([
                'key' => 'web_sms_code_' . $phone,
                'expired_at' => $expiredAt->toDateTimeString(),
            ])->setStatusCode(201);
        }
        else
            return $this->response->error('短信发送异常', 422);

    }
}
