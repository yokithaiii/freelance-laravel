<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;

class UserService
{

    public function getUserDetailInfo($user)
    {
        return $user->detailInfo;
    }

    public function getAuthId(): int|string|null
    {
        return Auth::id();
    }

    public function incrementUserJobCount($user): void
    {
        if ($user) {
            $user->detailInfo()->increment('jobs_count');
        }
    }

}