<?php

namespace Modules\Core\Support\Traits;

trait ResultTrait
{
    public function resultSuccess($data = null, $message = ''): array
    {
        return [
            'success' => true,
            'message' => $message,
            'data' => $data,
        ];
    }

    public function resultFail($data = null, $message = ''): array
    {
        return [
            'success' => false,
            'message' => $message,
            'data' => $data,
        ];
    }
}
