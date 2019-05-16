<?php
declare(strict_types=1);


namespace App\Controller;

use App\Authentication\AuthenticationHandler;
use App\Doctrine\Repository\UserRepository;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Eos\ComView\Server\Command\CommandProcessorInterface;
use Eos\ComView\Server\Exception\ViewNotFoundException;
use Eos\ComView\Server\Model\Value\ViewRequest;
use Eos\ComView\Server\View\ViewInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Paul Martin Gütschow <guetschow@esonewmedia.de>
 */
class SecurityController extends AbstractController
{

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var CommandProcessorInterface
     */
    private $processor;

    /**
     * @var ViewInterface
     */
    private $views;
    /**
     * @var AuthenticationHandler
     */
    private $protectedAware;

    /**
     * @param UserRepository $userRepository
     * @param CommandProcessorInterface $processor
     * @param ViewInterface $views
     * @param AuthenticationHandler $protectedAware
     */
    public function __construct(
        UserRepository $userRepository,
        CommandProcessorInterface $processor,
        ViewInterface $views,
        AuthenticationHandler $protectedAware
    ) {
        $this->userRepository = $userRepository;
        $this->processor = $processor;
        $this->views = $views;
        $this->protectedAware = $protectedAware;
    }


    /**
     * @\Symfony\Component\Routing\Annotation\Route("/cv/me", name="me", methods={"GET"})
     *
     * @return JsonResponse
     * @throws ViewNotFoundException
     */
    public function me(): JsonResponse
    {
        $user = $this->protectedAware->getUser();
        $result = [];
        if ($user) {
            $data = $this->views->createView('showUserById', new ViewRequest(['id' => $user->getId()]));
            $result['user'] = $data->getData();
        }

        return new JsonResponse($result);
    }


    /**
     * @\Symfony\Component\Routing\Annotation\Route("/register", name="register", methods={"POST"})
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function register(Request $request): JsonResponse
    {
        $content = json_decode($request->getContent(), true);
        try {
            $this->processor->process('createUser', $content['parameters']);
        } catch (\Throwable $exception) {
            if ($exception instanceof UniqueConstraintViolationException) {
                return new JsonResponse(['error' => 'User with this ID or username (mail) already exists'], Response::HTTP_CONFLICT);
            }

            return new JsonResponse(['error' => 'Invalid data.'], Response::HTTP_BAD_REQUEST);
        }

        return new JsonResponse(['success' => 'User created.'], Response::HTTP_CREATED);
    }

    /**
     * @\Symfony\Component\Routing\Annotation\Route("/reset_password/user/{user}", name="reset_password")
     *
     * @param string $user
     * @return Response
     */
    public function resetPassword(string $user): Response
    {
        try {
            $this->processor->process('resetPassword', ['email' => $user]);
        } catch (\Throwable $exception) {
            return new JsonResponse(['error' => 'Invalid data.'], Response::HTTP_BAD_REQUEST);
        }

        return new JsonResponse();
    }

    /**
     * @\Symfony\Component\Routing\Annotation\Route("/reset_password/confirm/{token}", name="reset_password_confirm", methods={"GET", "POST"})
     *
     * @param Request $request
     * @param string $token
     * @return Response
     */
    public function resetPasswordCheck(Request $request, string $token): Response
    {

        try {
            $user = $this->userRepository->findOneBy(['token' => $token]);
            $content = json_decode($request->getContent(), true);
            $content['id'] = $user ? $user->getId() : null;
            $content['token'] = null;

            $this->processor->process('updateUser', $content);
        } catch (\Throwable $exception) {
            return new JsonResponse(['error' => 'Invalid Token'], Response::HTTP_BAD_REQUEST);
        }

        return new JsonResponse();
    }
}
