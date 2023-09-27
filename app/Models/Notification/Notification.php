<?php

namespace App\Models\Notification;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;



class Notification extends Model
{
    use HasFactory,
        SoftDeletes,
        NotificationAccessories,
        NotificationRelationShip,
        NotificationScope;

    protected $table = 'notifications';
    protected $guarded = [];
}
