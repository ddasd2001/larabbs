<?php

function route_class()
{
    return str_replace('.', '-', Route::currentRouteName());
}

function make_excerpt($value, $length = 200)
{
    $excerpt = trim(preg_replace('/\r\n|\r|\n+/', ' ', strip_tags($value)));
    return str_limit($excerpt, $length);
}

function model_admin_link($title, $model)
{
    return model_link($title, $model, 'admin');
}

function model_link($title, $model, $prefix = '')
{
    // 获取数据模型的复数蛇形命名
    $model_name = model_plural_name($model);

    // 初始化前缀
    $prefix = $prefix ? "/$prefix/" : '/';

    // 使用站点 URL 拼接全量 URL
    $url = config('app.url') . $prefix . $model_name . '/' . $model->id;

    // 拼接 HTML A 标签，并返回
    return '<a href="' . $url . '" target="_blank">' . $title . '</a>';
}

function model_plural_name($model)
{
    // 从实体中获取完整类名，例如：App\Models\User
    $full_class_name = get_class($model);

    // 获取基础类名，例如：传参 `App\Models\User` 会得到 `User`
    $class_name = class_basename($full_class_name);

    // 蛇形命名，例如：传参 `User`  会得到 `user`, `FooBar` 会得到 `foo_bar`
    $snake_case_name = snake_case($class_name);

    // 获取子串的复数形式，例如：传参 `user` 会得到 `users`
    return str_plural($snake_case_name);
}

function send_sms($phone)//发送短信验证码
{
    $url = 'http://api.submail.cn/message/xsend.json';
    $code = (string)rand(100000, 999999);
    $code_arr = ['code'=>$code];
    $code_json = json_encode($code_arr);
    $post = 'appid=10714&to='.$phone.'&project=giLvt&signature=883e920261fd0c91bbf8a22c918b9eba&vars='.$code_json;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);//要求结果为字符串且输出到屏幕上
    curl_setopt($ch, CURLOPT_POST, TRUE);//post提交方式
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
    $result = curl_exec($ch);//运行curl
    curl_close($ch);
    $result = json_decode($result,true);
    if ($result['status'] == 'success')
    {
        Redis::set('web_sms_code_' . $phone, $code);
        Redis::expire('web_sms_code_' . $phone, 18000);//5分钟失效
        return true;
    }
    else
        return false;
}