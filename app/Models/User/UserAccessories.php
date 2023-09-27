<?php

namespace App\Models\User;

trait UserAccessories
{

    public function getAvatar(): int | string
    {
        if ($this->getFirstMediaUrl('users') == '') {
            return '';
        }
        return $this->getFirstMediaUrl('users');
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }


    public function getStatusArNameAttribute()
    {
        if ($this->status) {
            return 'نشط';
        }
        return 'غير نشط';
    }

    public function getStatusEnNameAttribute()
    {
        if ($this->status) {
            return 'active';
        }
        return 'inactive';
    }


}
