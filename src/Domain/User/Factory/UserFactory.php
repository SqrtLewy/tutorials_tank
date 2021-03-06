<?php

namespace App\Domain\User\Factory;

use App\Domain\Common\ValueObject\AggregateRootId;
use App\Domain\User\User;
use App\Domain\User\ValueObject\Avatar;
use App\Domain\User\ValueObject\Email;
use App\Domain\User\ValueObject\Password;
use App\Domain\User\ValueObject\Roles;
use App\Domain\User\ValueObject\Steemit;
use App\Domain\User\ValueObject\UserName;

/**
 * Class UserFactory.
 */
class UserFactory
{
    /**
     * @param AggregateRootId $id
     * @param UserName        $username
     * @param Email           $email
     * @param Roles           $roles
     * @param Avatar          $avatar
     * @param Steemit         $steemit
     * @param bool            $banned
     * @param Password        $password
     *
     * @return mixed
     */
    public static function create(AggregateRootId $id, UserName $username, Email $email, Roles $roles, Avatar $avatar, Steemit $steemit, bool $banned, Password $password)
    {
        $user = User::create($id, $username, $email, $roles, $avatar, $steemit, $banned, $password);

        return $user;
    }
}
