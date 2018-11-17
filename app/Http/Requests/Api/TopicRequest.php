<?php

namespace App\Http\Requests\Api;


class TopicRequest extends FormRequest
{
    public function rules()
    {
        if ($this->method() == 'POST')
        {
            return [
                'title' => 'required|string',
                'body' => 'required|string',
                'category_id' => 'required|exists:categories,id',
            ];
        }
        elseif ($this->method() == 'PATCH')
        {
            return [
                'title' => 'string',
                'body' => 'string',
                'category_id' => 'exists:categories,id',
            ];
        }

    }

    public function attributes()
    {
        return [
            'title' => '标题',
            'body' => '话题内容',
            'category_id' => '分类',
        ];
    }
}