<?php

namespace Modules\Core\Support;

class ResponseFormat extends \Jiannei\Response\Laravel\Support\Format
{
    public function data(?array $data, ?string $message, int $code, $errors = null): array
    {
        $formatData = [
            'status' => $this->formatStatus($code),
            'code' => $code,
            'message' => $this->formatMessage($code, $message),
            'data' => $data ?: (object) $data,
            'error' => $errors ?: (object) [], /** @phpstan-ignore-line */
            'request_time' => request()->server('REQUEST_TIME'),
            'request_id' => app('request_id'),
        ];

        return $this->formatDataFields($formatData, config('response.format.fields', []));
    }
}
