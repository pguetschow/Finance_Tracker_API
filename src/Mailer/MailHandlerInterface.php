<?php
declare(strict_types=1);

namespace App\Mailer;


use App\Entity\User;

/**
 * @author Paul Martin Gütschow <guetschow@esonewmedia.de>
 */
interface MailHandlerInterface
{
    /**
     * @param User $user
     */
    public function resetPasswordMail(User $user): void;

    /**
     * @param User $user
     * @param string $output
     * @throws \Exception
     */
    public function gdprExportMail(User $user, string $output): void;

}
