<?php
declare(strict_types=1);


namespace App\ComView\View;

use App\Doctrine\Entity\Entry;
use App\Doctrine\Repository\EntryRepository;
use Eos\ComView\Server\Exception\InvalidRequestException;
use Eos\ComView\Server\Exception\ViewNotFoundException;
use Eos\ComView\Server\Model\Value\ViewRequest;
use Eos\ComView\Server\Model\Value\ViewResponse;

/**
 * @author Paul Martin Gütschow <guetschow@esonewmedia.de>
 */
class ShowEntryById extends AbstractView
{

    /**
     * @var EntryRepository
     */
    private $entryRepository;

    /**
     * @param EntryRepository $entryRepository
     */
    public function __construct(EntryRepository $entryRepository)
    {
        $this->entryRepository = $entryRepository;
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
            throw new InvalidRequestException('Missing ID');
        }

        $entry = $this->entryRepository->find($request->getParameters()['id']);

        if (!$entry instanceof Entry) {
            throw new ViewNotFoundException('showUserById');
        }

        return $this->createResponse(
            $request,
            [
                'name' => $entry->getName(),
                'amount' => $entry->getAmount(),
                'date' => $entry->getDate()->format('Y-m-d'),
                'category' => $entry->getCategory(),
            ]
        );
    }


}
