<?php

namespace Modules\Core\Support;

class ResponseFormat extends \Jiannei\Response\Laravel\Support\Format
{
    public function data(mixed $data = null, string $message = '', int|\BackedEnum $code = 200, $error = null): static
    {
        return tap($this, function () use ($data, $message, $code, $error) {
            $this->statusCode = $this->formatStatusCode($this->formatBusinessCode($code), $data);

            $this->data = $this->formatDataFields([
                'status' => $this->formatStatus($code),
                'code' => $this->formatBusinessCode($code),
                'message' => $this->formatMessage($this->formatBusinessCode($code), $message),
                'data' => $this->formatData($data),
                'error' => $this->formatError($error),
                'request_time' => request()->server('REQUEST_TIME'),
                'request_id' => app('request_id'),
            ]);
        });
    }
}
