<?php

namespace Modules\Core\Services;

use Illuminate\Support\Traits\Macroable;
use Modules\Core\Support\Traits\CreateStaticable;

abstract class BaseService
{
    use CreateStaticable;
    use Macroable;
}
