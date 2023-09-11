<?php

declare(strict_types=1);

namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Core\Models\Concerns\AllowedFilterable;
use Modules\Core\Models\Concerns\Filterable;
use Modules\Core\Models\Concerns\ForceUseIndexable;
use Modules\Core\Models\Concerns\Observable;
use Modules\Core\Models\Concerns\Pipeable;
use Modules\Core\Models\Concerns\SerializeDate;
use Modules\Core\Models\Concerns\Sortable;

class BaseModel extends Model
{
    use AllowedFilterable;
    use Filterable;
    use ForceUseIndexable;
    use Observable;
    use Pipeable;
    use SerializeDate;
    use Sortable;
}
