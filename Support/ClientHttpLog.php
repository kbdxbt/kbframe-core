<?php

namespace Modules\Core\Support;

use Illuminate\Http\Client\Events\ResponseReceived;
use Modules\Core\Support\CurlFormatter;
use Modules\Core\Support\HttpLog;

class ClientHttpLog extends HttpLog
{
    public function handle(ResponseReceived $event): void
    {
        $this->createHttpLog($event->request, $event->response);
    }

    protected function collectData($request, $response): array
    {
        $currentTime = microtime(true);

        return [
            'ip' => substr($response->transferStats->getHandlerStat('local_ip'), 0, 16),
            'url' => substr($request->url(), 0, 2083),
            'method' => substr($request->method(), 0, 10),
            'request_id' => substr(app('request_id'), 0, 50),
            'request_params' => substr($this->extractInput($request->data()), 0, $this->maxLengthOfMediumtext),
            'request_header' => substr($this->extractHeader($request->headers()), 0, $this->maxLengthOfMediumtext),
            'request_time' => (int) (($currentTime * 1000000 -
                    $response->transferStats->getHandlerStat('total_time_us')) / 1000000),
            'response_code' => $response->status(),
            'response_header' => substr($this->extractHeader($response), 0, $this->maxLengthOfMediumtext),
            'response_body' => substr($response->body(), 0, $this->maxLengthOfMediumtext),
            'response_time' => (int) $currentTime,
            'duration' => substr((string) $response->transferStats->getTransferTime(), 0, 10),
            'curl_text' => (new CurlFormatter($this->maxLengthOfMediumtext))->format($request->toPsrRequest()),
            'device' => $request->header('User-Agent.0'),
            'version' => config('app.version'),
            'ext' => json_encode($response->transferStats->getHandlerStats()),
        ];
    }
}
