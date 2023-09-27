<?php

namespace App\Models\Geolocation;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;



class Geolocation extends Model
{
    use HasFactory,
        SoftDeletes,
        GeolocationAccessories,
        GeolocationRelationShip,
        GeolocationScope;

        protected $table = 'geolocations';
        protected $guarded = [];
 }
