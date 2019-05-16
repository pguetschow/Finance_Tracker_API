<?php
declare(strict_types=1);


namespace App\ComView\View;

use App\Doctrine\Entity\User;
use App\Doctrine\Repository\UserRepository;
use Eos\ComView\Server\Exception\InvalidRequestException;
use Eos\ComView\Server\Exception\ViewNotFoundException;
use Eos\ComView\Server\Model\Value\ViewRequest;
use Eos\ComView\Server\Model\Value\ViewResponse;

/**
 * @author Paul Martin Gütschow <guetschow@esonewmedia.de>
 */
class ShowUserById extends AbstractView
{

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
     * @param string $name
     * @param ViewRequest $request
     * @return ViewResponse
     * @throws InvalidRequestException
     * @throws ViewNotFoundException
     */
    public function createView(string $name, ViewRequest $request): ViewResponse
    {

        if (!\array_key_exists('id', $request->getParameters())) {
            throw new InvalidRequestException('Missing ID!');
        }
        $user = $this->userRepository->find($request->getParameters()['id']);

        if (!$user instanceof User) {
            throw new ViewNotFoundException('showUserById');
        }

        return $this->createResponse(
            $request,
            [
                'id' => $user->getId(),
                'username' => $user->getUsername(),
                'firstName' => $user->getFirstName(),
                'lastName' => $user->getLastName(),
                'createdAt' => $user->getCreatedAt()->format(DATE_ATOM),
            ]
        );
    }


}
