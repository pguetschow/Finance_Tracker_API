<?php
declare(strict_types=1);


namespace App\ComView\CommandProcessor;

use App\Doctrine\Entity\Category;
use App\Doctrine\Entity\Entry;
use App\Doctrine\Repository\CategoryRepository;
use App\Doctrine\Repository\EntryRepository;
use App\Doctrine\Repository\UserRepository;
use Eos\ComView\Server\Command\CommandProcessorInterface;
use Eos\ComView\Server\Exception\CommandNotFoundException;
use Eos\ComView\Server\Model\Value\CommandResponse;

/**
 * @author Paul Martin Gütschow <guetschow@esonewmedia.de>
 */
class CreateEntry implements CommandProcessorInterface
{

    /**
     * @var EntryRepository
     */
    private $entryRepository;

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var CategoryRepository
     */
    private $categoryRepository;

    /**
     * @param EntryRepository $entryRepository
     * @param UserRepository $userRepository
     * @param CategoryRepository $categoryRepository
     */
    public function __construct(EntryRepository $entryRepository, UserRepository $userRepository, CategoryRepository $categoryRepository)
    {
        $this->entryRepository = $entryRepository;
        $this->userRepository = $userRepository;
        $this->categoryRepository = $categoryRepository;
    }


    /**
     * @param string $name
     * @param array $request
     * @return CommandResponse
     * @throws CommandNotFoundException
     * @throws \Exception
     */
    public function process(string $name, array $request): CommandResponse
    {
        if ($name !== 'createEntry') {
            throw new CommandNotFoundException($name);
        }


        $user = $this->userRepository->findAll()[0];
        $category = $this->categoryRepository->findOneBy(['name' => $request['category']]);
        if (!$category instanceof Category) {
            $category = new Category();
            $category->setName($request['category']);
            $this->categoryRepository->save($category);
        }


        $result = [];

        $entry = new Entry();
        try {
            $entry
                ->setId($request['id'])
                ->setName($request['name'])
                ->setAmount($request['amount'])
                ->setDate(\DateTime::createFromFormat('Y-m-d', $request['date']))
                ->setUser($user)
                ->setCategory($category);
            $status = 'SUCCESS';
        } catch (\Exception $exception) {
            $status = 'ERROR';
        }


        $this->entryRepository->save($entry);

        return new CommandResponse($status, $result);

    }


}
