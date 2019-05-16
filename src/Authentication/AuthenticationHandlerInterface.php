<?php
declare(strict_types=1);

namespace App\Authentication;


use App\Doctrine\Entity\User;

/**
 * @author Paul Martin Gütschow <guetschow@esonewmedia.de>
 */
interface AuthenticationHandlerInterface
{
    /**
     * @return User|null
     */
    public function getUser(): ?User;

    /**
     * @param string|null $username
     * @return AuthenticationHandler
     */
    public function setUserName(?string $username): AuthenticationHandler;
}
