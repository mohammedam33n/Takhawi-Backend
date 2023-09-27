<?php

namespace App\Models\Admin;

trait AdminAccessories
{

    public function getAvatar(): int | string
    {
        if ($this->getFirstMediaUrl('admins') == '') {
            return '';
        }
        return $this->getFirstMediaUrl('admins');
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function getStatusNameAttribute()
    {
        if ($this->status) {
            return 'نشط';
        }
        return 'غير نشط';
    }


}
