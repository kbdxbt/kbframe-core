<?php

namespace Modules\Core\Services;

class LengthAwarePaginatorService extends \Illuminate\Pagination\LengthAwarePaginator
{
    public function toArray()
    {
        return [
            'total' => $this->total(),
            'page' => $this->currentPage(),
            'page_size' => $this->perPage(),
            'data' => $this->items->toArray(),
        ];
    }
}
