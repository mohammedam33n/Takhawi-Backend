<?php

namespace App\Models\RideRequest;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;



class RideRequest extends Model
{
    use HasFactory,
        SoftDeletes,
        RideRequestAccessories,
        RideRequestRelationShip,
        RideRequestScope;

    protected $table = 'ride_requests';
    protected $guarded = [];
}
