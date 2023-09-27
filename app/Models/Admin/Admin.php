<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;


class Admin extends Authenticatable implements JWTSubject 
{
    use HasFactory , 
        HasRoles , 
        Notifiable , 
        SoftDeletes , 
        AdminAccessories ,
        AdminRelationShip, 
        AdminScope;

    protected $guard_name = 'admin';
    protected $guarded = [];
    protected $appends = ['status_name'];
}
