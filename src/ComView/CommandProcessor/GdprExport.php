<?php
declare(strict_types=1);


namespace App\ComView\CommandProcessor;

use App\Entity\User;
use App\Helper\UserInfoExportHelper;
use App\Mailer\MailHandlerInterface;
use App\Repository\UserRepository;
use Eos\ComView\Server\Command\CommandProcessorInterface;
use Eos\ComView\Server\Exception\CommandNotFoundException;
use Eos\ComView\Server\Model\Value\CommandResponse;

/**
 * @author Paul Martin Gütschow <guetschow@esonewmedia.de>
 */
class GdprExport implements CommandProcessorInterface
{

    /**
     * @var UserInfoExportHelper
     */
    private $exportHelper;

    /**
     * @var MailHandlerInterface
     */
    private $mailer;

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @param UserInfoExportHelper $exportHelper
     * @param MailHandlerInterface $mailer
     * @param UserRepository $userRepository
     */
    public function __construct(UserInfoExportHelper $exportHelper, MailHandlerInterface $mailer, UserRepository $userRepository)
    {
        $this->exportHelper = $exportHelper;
        $this->mailer = $mailer;
        $this->userRepository = $userRepository;
    }


    /**
     * @param string $name
     * @param array $request
     * @return CommandResponse
     * @throws \Exception
     */
    public function process(string $name, array $request): CommandResponse
    {
        if ($name !== 'gdprExport') {
            throw new CommandNotFoundException($name);
        }

        $user = $this->userRepository->findOneBy(['email' => $request['email']]);
        if (!$user instanceof User) {
            throw new \RuntimeException('User not found');
        }

        $body = $this->exportHelper->createExportPdf($user);


        $this->mailer->gdprExportMail($user, $body);

        return new CommandResponse('SUCCESS', []);

    }


}
