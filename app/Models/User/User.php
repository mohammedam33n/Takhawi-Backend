<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;


class User extends Authenticatable implements JWTSubject
{
    use HasFactory,
        SoftDeletes,
        UserAccessories,
        UserRelationShip,
        UserScope;

    protected $guard_name = 'user';
    protected $guarded = [];
    protected $appends = ['status_name'];
}
