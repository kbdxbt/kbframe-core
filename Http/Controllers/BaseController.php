<?php

declare(strict_types=1);

namespace Modules\Core\Http\Controllers;

use Illuminate\Routing\Controller;
use Modules\Core\Support\Traits\JsonResponseable;

class BaseController extends Controller
{
    use JsonResponseable;
}
