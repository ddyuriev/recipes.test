<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    const ADMIN_ROLE = 1;
    const USER_ROLE = 2;

    public $timestamps = false;
}
