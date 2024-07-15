<?php

declare(strict_types=1);

namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Core\Models\Concerns\HasOperator;
use Modules\Core\Models\Concerns\IndexHintsable;
use Modules\Core\Models\Concerns\Pipeable;
use Modules\Core\Models\Concerns\SerializeDate;

class BaseModel extends Model
{
    use IndexHintsable;
    use Pipeable;
    use SerializeDate;
    use HasOperator;

    protected $guarded = [];

    protected static function boot(): void
    {
        parent::boot();

        self::creating(function (Model $model) {
            $model->updateOperators();
        });

        self::updating(function (Model $model) {
            $model->updateOperators();
        });

        self::saving(function (Model $model) {
            $model->updateOperators();
        });
    }
}
