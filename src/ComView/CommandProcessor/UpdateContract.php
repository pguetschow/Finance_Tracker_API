<?php
declare(strict_types=1);


namespace App\ComView\CommandProcessor;

use App\Doctrine\Entity\Category;
use App\Doctrine\Entity\Contract;
use App\Doctrine\Repository\CategoryRepository;
use App\Doctrine\Repository\ContractRepository;
use Eos\ComView\Server\Command\CommandProcessorInterface;
use Eos\ComView\Server\Exception\CommandNotFoundException;
use Eos\ComView\Server\Model\Value\CommandResponse;

/**
 * @author Paul Martin Gütschow <guetschow@esonewmedia.de>
 */
class UpdateContract implements CommandProcessorInterface
{

    /**
     * @var ContractRepository
     */
    private $contractRepository;

    /**
     * @var CategoryRepository
     */
    private $categoryRepository;

    /**
     * @param ContractRepository $contractRepository
     * @param CategoryRepository $categoryRepository
     */
    public function __construct(ContractRepository $contractRepository, CategoryRepository $categoryRepository)
    {
        $this->contractRepository = $contractRepository;
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
        if ($name !== 'updateContract') {
            throw new CommandNotFoundException($name);
        }

        $result = [];
        try {

            $contract = $this->contractRepository->find($request['id']);

            if (!$contract instanceof Contract) {
                throw new \RuntimeException('Contract not found');
            }

            if (\array_key_exists('name', $request)) {
                $contract->setName($request['name']);
            }
            if (\array_key_exists('amount', $request)) {
                $contract->setAmount($request['amount']);
            }
            if (\array_key_exists('startDate', $request)) {
                $contract->setStartDate($request['startDate']);
            }
            if (\array_key_exists('endDate', $request)) {
                $contract->setEndDate($request['endDate']);
            }
            if (\array_key_exists('interval', $request)) {
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
            }

            if (\array_key_exists('category', $request)) {
                $category = $this->categoryRepository->findOneBy(['name' => $request['category']]);
                if (!$category instanceof Category) {
                    $category = new Category();
                    $category->setName($request['category']);
                    $this->categoryRepository->save($category);
                }
                $contract->setCategory($category);
            }


            $this->contractRepository->save($contract);
            $status = 'SUCCESS';
        } catch (\Throwable $exception) {
            $status = 'ERROR';
        }

        return new CommandResponse($status, $result);

    }


}
