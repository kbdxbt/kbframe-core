<?php

declare(strict_types=1);

namespace Modules\Core\Http\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Collection;
use Modules\Core\Http\Resources\Concerns\ResourceAware;

class BaseCollection extends ResourceCollection
{
    use ResourceAware;

    public function toArray(Request $request): array|Collection|\JsonSerializable|Arrayable
    {
        return $this->collection->map(fn ($item) => $this->wrapCollect((array) $item, 'all'));
    }
}
