<?php
declare(strict_types=1);


namespace App\ComView\CommandProcessor;

use App\Entity\User;
use App\Mailer\MailHandlerInterface;
use App\Repository\UserRepository;
use Eos\ComView\Server\Command\CommandProcessorInterface;
use Eos\ComView\Server\Exception\CommandNotFoundException;
use Eos\ComView\Server\Model\Value\CommandResponse;

/**
 * @author Paul Martin Gütschow <guetschow@esonewmedia.de>
 */
class ResetPassword implements CommandProcessorInterface
{

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var MailHandlerInterface
     */
    private $mailer;

    /**
     * @param UserRepository $userRepository
     * @param MailHandlerInterface $mailer
     */
    public function __construct(UserRepository $userRepository, MailHandlerInterface $mailer)
    {
        $this->userRepository = $userRepository;
        $this->mailer = $mailer;
    }


    /**
     * @param string $name
     * @param array $request
     * @return CommandResponse
     * @throws CommandNotFoundException
     */
    public function process(string $name, array $request): CommandResponse
    {
        if ($name !== 'resetPassword') {
            throw new CommandNotFoundException($name);
        }

        $token = uniqid('reset_', false);

        $user = $this->userRepository->findOneBy(['email' => $request['email']]);
        if (!$user instanceof User) {
            throw new \RuntimeException('User not found');
        }
        $user->setToken($token);

        $this->userRepository->save($user);

        $this->mailer->resetPasswordMail($user);

        return new CommandResponse('SUCCESS', []);

    }


}
