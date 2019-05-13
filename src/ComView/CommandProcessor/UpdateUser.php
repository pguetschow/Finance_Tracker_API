<?php
declare(strict_types=1);


namespace App\ComView\CommandProcessor;

use App\Entity\User;
use App\Repository\UserRepository;
use Eos\ComView\Server\Command\CommandProcessorInterface;
use Eos\ComView\Server\Exception\CommandNotFoundException;
use Eos\ComView\Server\Model\Value\CommandResponse;
use Symfony\Component\Security\Core\Encoder\Argon2iPasswordEncoder;

/**
 * @author Paul Martin Gütschow <guetschow@esonewmedia.de>
 */
class UpdateUser implements CommandProcessorInterface
{

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var Argon2iPasswordEncoder
     */
    private $passwordEncoder;

    /**
     * @param UserRepository $userRepository
     * @param Argon2iPasswordEncoder $passwordEncoder
     */
    public function __construct(UserRepository $userRepository, Argon2iPasswordEncoder $passwordEncoder)
    {
        $this->userRepository = $userRepository;
        $this->passwordEncoder = $passwordEncoder;
    }


    /**
     * @param string $name
     * @param array $request
     * @return CommandResponse
     * @throws CommandNotFoundException
     */
    public function process(string $name, array $request): CommandResponse
    {
        if ($name !== 'updateUser') {
            throw new CommandNotFoundException($name);
        }

        $result = [];
        try {

            $user = $this->userRepository->find($request['id']);
            if (!$user instanceof User) {
                throw new \RuntimeException('User not found');
            }

            if (\array_key_exists('firstName', $request)) {
                $user->setFirstName($request['firstName']);
            }
            if (\array_key_exists('lastName', $request)) {
                $user->setLastName($request['lastName']);
            }
            if (\array_key_exists('email', $request)) {
                $user->setEmail($request['email']);
            }
            if (\array_key_exists('token', $request)) {
                $user->setEmail($request['token']);
            }

            if (\array_key_exists('password', $request)) {
                $password = $this->passwordEncoder->encodePassword($request['password'], $user->getSalt());
                $user->setPassword($password);
            }


            $this->userRepository->save($user);
            $status = 'SUCCESS';
        } catch (\Throwable $exception) {
            $status = 'ERROR';
        }

        return new CommandResponse($status, $result);

    }


}
