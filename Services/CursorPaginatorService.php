<?php

namespace Modules\Core\Services;

class CursorPaginatorService extends \Illuminate\Pagination\CursorPaginator
{
    public function toArray()
    {
        return [
            'data' => $this->items->toArray(),
            'per_page' => $this->perPage(),
            'next_cursor' => $this->nextCursor()?->encode(),
            'prev_cursor' => $this->previousCursor()?->encode(),
        ];
    }
}
