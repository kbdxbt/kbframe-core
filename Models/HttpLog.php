<?php

namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class HttpLog extends BaseModel
{
    use SoftDeletes;

    protected $table = 'http_log';

    protected $guarded = [];

    protected $casts = [
        'ext' => 'json',
    ];
}
