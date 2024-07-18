<?php

namespace Modules\Core\Services;

use Illuminate\Support\Traits\Macroable;
use Modules\Core\Support\Traits\CreateStaticable;
use Modules\Core\Support\Traits\ResultTrait;

abstract class BaseService
{
    use CreateStaticable;
    use Macroable;
    use ResultTrait;
}
