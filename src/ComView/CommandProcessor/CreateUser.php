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
class CreateUser implements CommandProcessorInterface
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
        if ($name !== 'createUser') {
            throw new CommandNotFoundException($name);
        }

        $result = [];
        $salt = uniqid('salt_', false);
        $user = new User();
        $user
            ->setId($request['id'])
            ->setFirstName($request['firstName'])
            ->setLastName($request['lastName'])
            ->setEmail($request['email'])
            ->setActive(false);

        $password = $this->passwordEncoder->encodePassword($request['password'], $salt);
        $user->setPassword($password)->setSalt($salt);

        $this->userRepository->save($user);

        return new CommandResponse('SUCCESS', $result);

    }


}
