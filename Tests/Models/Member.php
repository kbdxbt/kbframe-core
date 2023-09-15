<?php

namespace Modules\Core\Tests\Models;

use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    protected $fillable = [
        'name', 'email', 'password',
    ];
}
