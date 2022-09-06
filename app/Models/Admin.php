<?php

namespace App\Models;
// namespace App;

use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;


//this is newx
use App\Traits\MustVerifyEmail;


class Admin extends Model implements AuthenticatableContract, AuthorizableContract, JWTSubject ,CanResetPasswordContract
{

    use Authenticatable, Authorizable, Notifiable, MustVerifyEmail,CanResetPassword;
/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'mac_name','email'
    ];
/**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
    ];
/**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
    ];
/**
   * Get the identifier that will be stored in the subject claim of the JWT.
   *
   * @return mixed
   */
  public function getJWTIdentifier()
  {
      return $this->getKey();
  }
/**
   * Return a key value array, containing any custom claims to be added to the JWT.
   *
   * @return array
   */
  public function getJWTCustomClaims()
  {
      return [];
  }
protected static function boot()
  {
    parent::boot();
    
//     static::saved(function ($model) {
// /**
//       if( $model->isDirty('email') ) {
//         $model->setAttribute('', null);
//       }
//     });
   }
}