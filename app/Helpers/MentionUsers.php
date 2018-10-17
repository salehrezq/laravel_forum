<?php
/**
 * Created by PhpStorm.
 * User: Saleh
 * Date: 10/16/2018
 * Time: 3:02 AM
 */

namespace App\Helpers;
use App\User;
use Illuminate\Support\Facades\Notification;

class MentionUsers
{

    public static function mentionUsersIn($reply) {

        preg_match_all($reply->regexUsernameMention, $reply->body, $matches);

        $usernames = static::getUsernames(array_unique($matches[1]));

        if (count($usernames) > 0) {
            $usersIds = User::whereIn('username', $usernames)->get(['id']);
            $users = User::whereIn('id', $usersIds)->get();
            Notification::send($users, new \App\Notifications\UserMentionNotification($reply->id));
        }
    }

    /**
     * The purpose of this method is to exclude the owner
     * of the reply from receiving a mention from himself
     *
     * @param $matches
     * @return array
     */
    private static function getUsernames($matches) {

        $length = count($matches);

        $usernames = [];

        for ($i = 0; $i < $length; $i++) {

            $username = $matches[$i];

            if ($username === auth()->user()->username) {
                continue;
            }

            $usernames[] = $username;
        }

        return $usernames;
    }

}