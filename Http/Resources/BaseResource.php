<?php

declare(strict_types=1);

namespace Modules\Core\Http\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Core\Http\Resources\Concerns\ResourceAware;

class BaseResource extends JsonResource
{
    use ResourceAware;

    public function toArray(Request $request): array|\JsonSerializable|Arrayable
    {
        return $this->wrapCollect(parent::toArray($request));
    }
}
