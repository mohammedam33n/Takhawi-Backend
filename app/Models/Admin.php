<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Scout\Searchable;
use Laravel\Sanctum\HasApiTokens;

class Admin extends Authenticatable
{
    use HasFactory,Notifiable, HasRoles, Searchable, HasApiTokens;

    protected $guard = 'admin';

    function getPicAttribute($pic) {
        if (empty($pic)) {
            return asset('defult.jpg');
        }else {
            return asset($pic);
        }
    }

    protected $fillable = [
        'name', 'email', 'password', 'device_token'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray()
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'mobile' => $this->mobile,
        ];
    }
}
