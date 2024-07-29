<?php

declare(strict_types=1);

namespace Modules\Core\Http\Middleware;

use Modules\Core\Support\HttpLog;

class RequestLog extends HttpLog
{
    protected $removedHeaders = [
        'Authorization',
    ];

    protected $removedInputs = [
        'password',
        'password_confirmation',
        'new_password',
        'old_password',
    ];

    public function handle($request, $next)
    {
        return $next($request);
    }

    public function terminate($request, $response): void
    {
        $this->createHttpLog($request, $response);
    }

    protected function collectData($request, $response): array
    {
        return [
            'ip' => substr((string) $request->getClientIp(), 0, 16),
            'url' => substr($request->url(), 0, 128),
            'method' => substr($request->method(), 0, 10),
            'request_id' => substr(app('request_id'), 0, 50),
            'request_params' => substr($this->extractInput($request->input()), 0, $this->maxLengthOfMediumtext),
            'request_header' => substr($this->extractHeader($request->headers()), 0, $this->maxLengthOfMediumtext),
            'request_time' => (string) constant('LARAVEL_START'),
            'response_code' => (string) $response->status(),
            'response_header' => substr($this->extractHeader($request->headers()), 0, $this->maxLengthOfMediumtext),
            'response_body' => substr((string) $response->getContent(), 0, $this->maxLengthOfMediumtext),
            'response_time' => (string) microtime(true),
            'duration' => substr($this->calculateDuration(), 0, 10),
            'curl_text' => '',
            'device' => $request->header('User-Agent'),
            'version' => config('app.version'),
            'ext' => [],
        ];
    }
}
