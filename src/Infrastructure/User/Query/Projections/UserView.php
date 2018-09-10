<?php

namespace App\Infrastructure\User\Query\Projections;

use FOS\UserBundle\Model\User as BaseUser;

/**
 * Class UserView
 * @package App\Infrastructure\User\Query\Projections
 */
class UserView extends BaseUser
{
    /**
     * @var string
     */
    protected $id;

    public function __construct()
    {
        parent::__construct();
        // your own logic
    }

    /**
     * @var string
     */
    protected $avatar;

    /**
     * @var string
     */
    protected $steemit;

    /**
     * @var bool
     */
    protected $banned;

    /**
     * @return string
     */
    public function getAvatar(): string
    {
        return $this->avatar;
    }

    /**
     * @param string $avatar
     */
    public function setAvatar(string $avatar): void
    {
        $this->avatar = $avatar;
    }

    /**
     * @return string
     */
    public function getSteemit(): string
    {
        return $this->steemit;
    }

    /**
     * @param string $steemit
     */
    public function setSteemit(string $steemit): void
    {
        $this->steemit = $steemit;
    }

    /**
     * @return bool
     */
    public function isBanned(): bool
    {
        return $this->banned;
    }

    /**
     * @param bool $banned
     */
    public function setBanned(bool $banned): void
    {
        $this->banned = $banned;
    }

    /**
     * @param array $data
     * @return UserView
     */
    public static function deserializeProjections(array $data)
    {
        $userView = new self();
        $userView->id = $data['id'];
        $userView->username = $data['username'];
        $userView->avatar = $data['avatar'];
        $userView->steemit = $data['steemit'];
        $userView->banned = $data['banned'];
        $userView->email = $data['email'];
        $userView->emailCanonical = strtolower($data['email']);
        $userView->password = $data['password'];
        $userView->roles = $data['roles'];

        return $userView;
    }

//    /**
//     * @return array
//     */
//    public static function serializeProjections(): array
//    {
//        return [
//            'id' => $this->id,
//            'username' => $this->username,
//            'avatar' => $this->avatar,
//            'steemit' => $this->steemit,
//            'banned' => $this->banned,
//        ];
//    }
}