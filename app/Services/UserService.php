<?php

namespace App\Services;

use App\Helpers\SimpleValidator;
use App\Models\Token;
use App\Models\User;
use Carbon\Carbon;
use Tymon\JWTAuth\Exceptions\JWTException;

class UserService extends BaseService
{

    /**
     * @param $username string username or mobile
     * @param $password string
     * @return User|false
     */
    public function authenticateUser($username, $password)
    {
        if (SimpleValidator::mobile($username)) {
            $user = User::where('mobile', $username)->first();
        } else {
            $user = User::where('username', $username)->first();
        }
        return !empty($user) && \Hash::check($password, $user->password) ? $user : false;
    }

    public function markLoginSuccess(User $user)
    {
        $user->update(['login_ip' => get_client_ip(), 'login_at' => Carbon::now()]);
    }

    /**
     * @param $user User
     * @param $type int
     * @return Token|null
     */
    public function getUserToken(User $user, $type)
    {
        return $user->token()->where('type', $type)->first();
    }

    public function generateToken(User $user, $type)
    {
        $token = $this->getUserToken($user, $type);
        $newToken = \JWTAuth::fromUser($user);
        if (empty($token)) {
            $user->token()->create(['type' => $type, 'token' => $newToken]);
        } else {
            $this->saveToken($token, $newToken);
        }
        return $this->formattedToken($newToken, $user);
    }

    public function refreshToken(User $user, $type)
    {
        $token = $this->getUserToken($user, $type);
        if (empty($token)) {
            $this->throwServiceException();
        }

        $claims = \JWTAuth::manager()->getJWTProvider()->decode($token->token);
        if ($claims['jti'] != \JWTAuth::getClaim('key')) {
            $this->throwServiceException('invalid refresh token');
        }
        $newToken = \JWTAuth::claims(['key' => null])->fromUser($user);
        $this->saveToken($token, $newToken);
        return $this->formattedToken($newToken, $user);
    }

    protected function saveToken(Token $token, $newToken)
    {
        try {
            \JWTAuth::setToken($token->token)->invalidate();
        } catch (JWTException $e) {

        }
        $token->token = $newToken;
        $token->save();
    }

    protected function formattedToken($token, User $user)
    {
        $id = \JWTAuth::setToken($token)->getClaim('jti');
        $expiredTime = time() + config('jwt.ttl') * 60;
        $refreshTime = time() + config('jwt.refresh_ttl') * 60;
        $refreshToken = \JWTAuth::claims(['key' => $id, 'exp' => $refreshTime])->fromUser($user);
        return [
            'token' => $token,
            'expired_time' => $expiredTime,
            'refresh_token' => $refreshToken
        ];
    }

    /**
     * @param $filters
     * @param $orderBys [column => $direction]
     * @param int $offset
     * @param int $limit
     * @param array $select [column]
     * @return \Illuminate\Database\Eloquent\Collection [Model]
     */
    public function searchUser(array $filters, array $orderBys, $select = ['*'], $offset = 0, $limit = 1000)
    {
        $builder = User::select($select)->skip($offset)->limit($limit);
        foreach ($orderBys as $column => $direction) {
            $builder->orderBy($column, $direction);
        }
        return $builder->filter($filters)->get();
    }

    public function countUser(array $filters)
    {
        return User::filter($filters)->count();
    }

}