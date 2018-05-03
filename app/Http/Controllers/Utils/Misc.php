<?php

namespace App\Http\Controllers\Utils;

use Carbon\Carbon;
use App\User;


class Misc
{
    public static function generateApiToken($login)
    {
        return md5(uniqid($login, true));
    }

    public static function isTokenActual(User $user)
    {
        $tokenExpiredAt = Carbon::parse($user->token_created_at)->addHour(1);
        $timeNow = Carbon::now();

        if (Carbon::parse($user->token_created_at)->gt(Carbon::now())) {

        }
        return $timeNow < $tokenExpiredAt;
    }
}