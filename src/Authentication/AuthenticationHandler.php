<?php
declare(strict_types=1);


namespace App\Authentication;

use App\Doctrine\Entity\User;
use App\Doctrine\Repository\UserRepository;

/**
 * @author Paul Martin Gütschow <guetschow@esonewmedia.de>
 */
class AuthenticationHandler implements AuthenticationHandlerInterface
{
    /**
     * @var User|null
     */
    private $user;

    /**
     * @var string|null
     */
    private $username;

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }


    /**
     * @return User|null
     */
    public function getUser(): ?User
    {
        if (!$this->user instanceof User && $this->username) {
            $this->user = $this->userRepository->findOneBy(['email' => $this->username]);
        }

        return $this->user;
    }

    /**
     * @param string|null $username
     * @return AuthenticationHandler
     */
    public function setUserName(?string $username): AuthenticationHandler
    {
        $this->username = $username;

        return $this;
    }


}
