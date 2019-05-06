<?php
declare(strict_types=1);


namespace App\ComView\CommandProcessor;

use App\Entity\Category;
use App\Entity\Contract;
use App\Repository\CategoryRepository;
use App\Repository\ContractRepository;
use App\Repository\UserRepository;
use Eos\ComView\Server\Command\CommandProcessorInterface;
use Eos\ComView\Server\Exception\CommandNotFoundException;
use Eos\ComView\Server\Model\Value\CommandResponse;

/**
 * @author Paul Martin Gütschow <guetschow@esonewmedia.de>
 */
class CreateContract implements CommandProcessorInterface
{

    /**
     * @var ContractRepository
     */
    private $contractRepository;

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var CategoryRepository
     */
    private $categoryRepository;

    /**
     * @param ContractRepository $contractRepository
     * @param UserRepository $userRepository
     * @param CategoryRepository $categoryRepository
     */
    public function __construct(ContractRepository $contractRepository, UserRepository $userRepository, CategoryRepository $categoryRepository)
    {
        $this->contractRepository = $contractRepository;
        $this->userRepository = $userRepository;
        $this->categoryRepository = $categoryRepository;
    }


    /**
     * @param string $name
     * @param array $request
     * @return CommandResponse
     * @throws \Exception
     */
    public function process(string $name, array $request): CommandResponse
    {
        if ($name !== 'createContract') {
            throw new CommandNotFoundException($name);
        }

        $result = [];
        $user = $this->userRepository->findAll()[0];

        $category = $this->categoryRepository->findOneBy(['name' => $request['category']]);
        if (!$category instanceof Category) {
            $category = new Category();
            $category->setName($request['category']);
            $this->categoryRepository->save($category);
        }

        $contract = new Contract();
        try {
            $contract
                ->setId($request['id'])
                ->setName($request['name'])
                ->setAmount($request['amount'])
                ->setStartDate(\DateTime::createFromFormat('Y-m-d', $request['startDate']))
                ->setEndDate(\DateTime::createFromFormat('Y-m-d', $request['endDate']))
                ->setUser($user)
                ->setCategory($category);

            switch ($request['interval']) {
                case 'annually':
                    $rrule = Contract::ANNUALLY;
                    break;
                case 'biannually':
                    $rrule = Contract::BIANNUALLY;
                    break;
                case 'quarterly':
                    $rrule = Contract::QUARTERLY;
                    break;
                case 'monthly':
                default:
                    $rrule = Contract::MONTHLY;
                    break;
            }
            $contract->setDueInterval($rrule);
            $status = 'SUCCESS';
        } catch (\Exception $exception) {
            $status = 'ERROR';
        }


        $this->contractRepository->save($contract);

        return new CommandResponse($status, $result);

    }


}
