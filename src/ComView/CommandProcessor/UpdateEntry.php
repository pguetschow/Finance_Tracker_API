<?php
declare(strict_types=1);


namespace App\ComView\CommandProcessor;

use App\Entity\Category;
use App\Entity\Entry;
use App\Repository\CategoryRepository;
use App\Repository\EntryRepository;
use Eos\ComView\Server\Command\CommandProcessorInterface;
use Eos\ComView\Server\Exception\CommandNotFoundException;
use Eos\ComView\Server\Model\Value\CommandResponse;

/**
 * @author Paul Martin Gütschow <guetschow@esonewmedia.de>
 */
class UpdateEntry implements CommandProcessorInterface
{

    /**
     * @var EntryRepository
     */
    private $entryRepository;

    /**
     * @var CategoryRepository
     */
    private $categoryRepository;

    /**
     * @param EntryRepository $entryRepository
     * @param CategoryRepository $categoryRepository
     */
    public function __construct(EntryRepository $entryRepository, CategoryRepository $categoryRepository)
    {
        $this->entryRepository = $entryRepository;
        $this->categoryRepository = $categoryRepository;
    }


    /**
     * @param string $name
     * @param array $request
     * @return CommandResponse
     * @throws CommandNotFoundException
     */
    public function process(string $name, array $request): CommandResponse
    {
        if ($name !== 'updateEntry') {
            throw new CommandNotFoundException($name);
        }

        $result = [];
        try {

            $entry = $this->entryRepository->find($request['id']);

            if (!$entry instanceof Entry) {
                throw new \RuntimeException('Entry not found');
            }

            if (\array_key_exists('name', $request)) {
                $entry->setName($request['name']);
            }
            if (\array_key_exists('amount', $request)) {
                $entry->setAmount($request['amount']);
            }
            if (\array_key_exists('date', $request)) {
                $entry->setDate($request['date']);
            }

            if (\array_key_exists('category', $request)) {
                $category = $this->categoryRepository->findOneBy(['name' => $request['category']]);
                if (!$category instanceof Category) {
                    $category = new Category();
                    $category->setName($request['category']);
                    $this->categoryRepository->save($category);
                }
                $entry->setCategory($category);
            }


            $this->entryRepository->save($entry);
            $status = 'SUCCESS';
        } catch (\Throwable $exception) {
            $status = 'ERROR';
        }

        return new CommandResponse($status, $result);

    }


}
