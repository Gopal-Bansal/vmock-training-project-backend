<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use App\Traits\MustVerifyEmail;

use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;


 //  use Tymon\JWTAuth\Contracts\JWTSubject;   for jwt 
 


class Task extends Model implements AuthenticatableContract, AuthorizableContract,JWTSubject,CanResetPasswordContract
{
    use Authenticatable, Authorizable, HasFactory,Notifiable,MustVerifyEmail,CanResetPassword;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'task';
    protected $fillable = [
        'title','description', 'due_date', 'assigned_to', 'assigned_by'
    ];
/**
     
     * The attributes that should be cast to native types.

   * Get the identifier that will be stored in the subject claim of the JWT.
   *
   * @return mixed
   */
  
/**
   * Return a key value array, containing any custom claims to be added to the JWT.
   *
   * @return array
   */

  public function getJWTIdentifier()
  {
      return $this->getKey();
  }
  public function getJWTCustomClaims()
  {
      return [];
  }
  
protected static function boot()
  {
    parent::boot();
    
    static::saved(function ($model) {

      if( $model->isDirty('email') ) {
        $model->setAttribute('email_verified_at', null);
        $model->sendEmailVerificationNotification();
      }
});


}
}




