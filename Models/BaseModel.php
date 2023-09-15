<?php

declare(strict_types=1);

namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Core\Models\Concerns\SerializeDate;

class BaseModel extends Model
{
    use SerializeDate;
}
