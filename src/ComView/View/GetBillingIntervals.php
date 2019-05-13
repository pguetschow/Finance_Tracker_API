<?php
declare(strict_types=1);


namespace App\ComView\View;

use App\Entity\Contract;
use App\Helper\AuthenticationAwareHelper;
use App\Repository\ContractRepository;
use Eos\ComView\Server\Model\Value\ViewRequest;
use Eos\ComView\Server\Model\Value\ViewResponse;

/**
 * @author Paul Martin Gütschow <guetschow@esonewmedia.de>
 */
class GetBillingIntervals extends AbstractView
{

    /**
     * @var AuthenticationAwareHelper
     */
    private $protectedAware;

    /**
     * @var ContractRepository
     */
    private $contractRepository;

    /**
     * @param AuthenticationAwareHelper $protectedAware
     * @param ContractRepository $contractRepository
     */
    public function __construct(AuthenticationAwareHelper $protectedAware, ContractRepository $contractRepository)
    {
        $this->protectedAware = $protectedAware;
        $this->contractRepository = $contractRepository;
    }


    /**
     * @param string $name
     * @param ViewRequest $request
     * @return ViewResponse
     */
    public function createView(string $name, ViewRequest $request): ViewResponse
    {
        $user = $this->protectedAware->getUser();
        $contracts = $this->contractRepository->findBy(['user' => $user]);
        $result = [];

        /** @var Contract $contract */
        foreach ($contracts as $contract) {
            $categoryName = $contract->getCategory() !== null ? $contract->getCategory()->getName() : 'undefined';
            $result[$contract->getDueInterval()][] = [
                'name' => $contract->getName(),
                'amount' => $contract->getAmount(),
                'startDate' => $contract->getStartDate()->format('Y-m-d'),
                'endDate' => $contract->getEndDate() ? $contract->getEndDate()->format('Y-m-d') : null,
                'category' => $categoryName,
            ];
        }


        return $this->createResponse(
            $request,
            $result
        );
    }


}
