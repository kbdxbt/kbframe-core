<?php

declare(strict_types=1);

namespace Modules\Core\Http\Controllers;

use Illuminate\Routing\Controller;
use Modules\Core\Support\Traits\ActionControllerTrait;

class BaseController extends Controller
{
    use ActionControllerTrait;
}
