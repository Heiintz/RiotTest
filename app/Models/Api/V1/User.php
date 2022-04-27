<?php

namespace App\Models\Api\V1;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Get the permissions for the user.
     */
    public function permissions()
    {
        return $this->belongsToMany('App\Models\Api\V1\Permissions', 'users_permissions', 'users_id','permissions_id');
    }

    /**
     * Get the permissions for the user.
     */
    public function permissionsNames()
    {
        return $this->permissions()->getOnlyName();
    }

     /**
     * Get the basic infos for the user.
     */
    public function scopeGetBasicUserInfos($query)
    {
        return $query->select([
            'id',
            'name',
            'email'
        ]);
    }

}
