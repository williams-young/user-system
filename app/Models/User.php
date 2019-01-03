<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    const STATE_DISABLED = 0;
    const STATE_ENABLED = 1;

    const STATES = [
        0 => '已禁用',
        1 => '启用中',
    ];

    protected $fillable = [
        'username', 'password', 'login_ip', 'login_at'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    public function stateName()
    {
        return array_key_exists($this->state, static::STATES) ? static::STATES[$this->state] : '';
    }

    public function detail()
    {
        return $this->hasOne(UserDetail::class);
    }

    public function token()
    {
        return $this->hasMany(Token::class);
    }

    public function scopeFilter(Builder $query, $filters)
    {
        $query->where(function (Builder $query) use ($filters) {
            empty($filters['username']) ?: $query->where('username', $filters['username']);
            empty($filters['state']) ?: $query->where('state', $filters['state']);
            empty($filters['login_after']) ?: $query->where('login_at', '>', $filters['login_after']);
        });
    }


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
}
