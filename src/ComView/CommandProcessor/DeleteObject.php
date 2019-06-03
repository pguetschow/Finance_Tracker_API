<?php
declare(strict_types=1);


namespace App\ComView\CommandProcessor;

use App\Doctrine\Repository\ContractRepository;
use App\Doctrine\Repository\EntryRepository;
use App\Doctrine\Repository\UserRepository;
use Eos\ComView\Server\Command\CommandProcessorInterface;
use Eos\ComView\Server\Model\Value\CommandResponse;

/**
 * @author Paul Martin Gütschow <guetschow@esonewmedia.de>
 */
class DeleteObject implements CommandProcessorInterface
{

    /**
     * @var ContractRepository
     */
    private $contractRepository;

    /**
     * @var EntryRepository
     */
    private $entryRepository;

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @param ContractRepository $contractRepository
     * @param EntryRepository $entryRepository
     * @param UserRepository $userRepository
     */
    public function __construct(ContractRepository $contractRepository, EntryRepository $entryRepository, UserRepository $userRepository)
    {
        $this->contractRepository = $contractRepository;
        $this->entryRepository = $entryRepository;
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

        try {
            $id = $request['id'];

            switch ($request['objectType']) {
                case 'contract':
                    $repository = $this->contractRepository;
                    break;
                case 'entry':
                    $repository = $this->entryRepository;
                    break;
                case 'user':
                    $repository = $this->userRepository;
                    break;
                default:
                    $repository = null;
            }

            if ($repository) {
                $object = $repository->find($id);
                $repository->delete($object);
                $status = 'SUCCESS';
            } else {
                $status = 'Nothing to delete. Check ID or objectType';
            }

        } catch (\Throwable $exception) {
            $status = 'ERROR';
        }

        return new CommandResponse($status, []);

    }


}
