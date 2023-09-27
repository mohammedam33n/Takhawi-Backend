<?php

namespace App\Models\DriverProfile;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;



class DriverProfile extends Model
{
    use HasFactory,
        SoftDeletes,
        DriverProfileAccessories,
        DriverProfileRelationShip,
        DriverProfileScope;

    protected $table = 'driver_profiles';
    protected $guarded = [];
}
