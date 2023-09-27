<?php

namespace App\Models\RideHistory;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;



class RideHistory extends Model
{
    use HasFactory,
        SoftDeletes,
        RideHistoryAccessories,
        RideHistoryRelationShip,
        RideHistoryScope;

    protected $table = 'ride_history';
    protected $guarded = [];
 }
