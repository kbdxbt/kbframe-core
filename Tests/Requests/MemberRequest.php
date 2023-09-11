<?php

namespace Modules\Core\Tests\Requests;

use Modules\Core\Http\Requests\BaseRequest;

class MemberRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function createRules(): array
    {
        return [
            'name' => ['required'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => '请输入名称',
        ];
    }
}