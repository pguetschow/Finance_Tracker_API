<?php
declare(strict_types=1);


namespace App\ComView\View;

use App\Entity\Contract;
use App\Repository\ContractRepository;
use Eos\ComView\Server\Exception\InvalidRequestException;
use Eos\ComView\Server\Exception\ViewNotFoundException;
use Eos\ComView\Server\Model\Value\ViewRequest;
use Eos\ComView\Server\Model\Value\ViewResponse;

/**
 * @author Paul Martin Gütschow <guetschow@esonewmedia.de>
 */
class ShowContractById extends AbstractView
{

    /**
     * @var ContractRepository
     */
    private $contractRepository;

    /**
     * @param ContractRepository $contractRepository
     */
    public function __construct(ContractRepository $contractRepository)
    {
        $this->contractRepository = $contractRepository;
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

        $contract = $this->contractRepository->find($request->getParameters()['id']);

        if (!$contract instanceof Contract) {
            throw new ViewNotFoundException('showUserById');
        }

        return $this->createResponse(
            $request,
            [
                'name' => $contract->getName(),
                'amount' => $contract->getAmount(),
                'startDate' => $contract->getStartDate()->format('Y-m-d'),
                'endDate' => $contract->getEndDate() ? $contract->getEndDate()->format('Y-m-d'): null ,
                'interval' => $contract->getDueInterval(),
                'category' => $contract->getCategory(),
            ]
        );
    }


}
