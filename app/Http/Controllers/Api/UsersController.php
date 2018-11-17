<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Transformers\UserTransformer;
use App\Http\Requests\Api\UserRequest;
use Illuminate\Support\Facades\Redis;
use App\Models\Image;

class UsersController extends Controller
{
    public function store(UserRequest $request)
    {
        $verification_key = $request->verification_key;
        $code = Redis::get($verification_key);
        if (!$code)
            return $this->response->error('验证码已失效', 422);
        if (!hash_equals($code, $request->verification_code))
            return $this->response->errorUnauthorized('验证码错误');

        $user = User::create([
            'name' => $request->name,
            'phone' => substr($verification_key, -11,11),
            'password' => bcrypt($request->password),
        ]);

        // 清除验证码缓存
        Redis::del($verification_key);

        return $this->response->item($user, new UserTransformer())
            ->setMeta([
                'access_token' => \Auth::guard('api')->fromUser($user),
                'token_type' => 'Bearer',
                'expires_in' => \Auth::guard('api')->factory()->getTTL() * 60
            ])
            ->setStatusCode(201);
    }

    public function me()
    {
        return $this->response->item($this->user(), new UserTransformer());
    }

    public function update(UserRequest $request)
    {
        $user = $this->user();

        $attributes = $request->only(['name', 'email', 'introduction']);

        if ($request->avatar_image_id) {
            $image = Image::find($request->avatar_image_id);

            $attributes['avatar'] = $image->path;
        }
        $user->update($attributes);

        return $this->response->item($user, new UserTransformer());
    }
}
