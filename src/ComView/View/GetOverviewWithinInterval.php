<?php
declare(strict_types=1);


namespace App\ComView\View;

use App\Authentication\AuthenticationHandlerInterface;
use App\Doctrine\Entity\Contract;
use App\Doctrine\Entity\Entry;
use App\Doctrine\Repository\ContractRepository;
use App\Doctrine\Repository\EntryRepository;
use Eos\ComView\Server\Model\Value\ViewRequest;
use Eos\ComView\Server\Model\Value\ViewResponse;
use Recurr\Rule;
use Recurr\Transformer\ArrayTransformer;

/**
 * @author Paul Martin Gütschow <guetschow@esonewmedia.de>
 */
class GetOverviewWithinInterval extends AbstractView
{
    /**
     * @var EntryRepository
     */
    private $entryRepository;

    /**
     * @var ContractRepository
     */
    private $contractRepository;

    /**
     * @var AuthenticationHandlerInterface
     */
    private $authenticationHandler;

    /**
     * @param EntryRepository $entryRepository
     * @param ContractRepository $contractRepository
     * @param AuthenticationHandlerInterface $authenticationHandler
     */
    public function __construct(
        EntryRepository $entryRepository,
        ContractRepository $contractRepository,
        AuthenticationHandlerInterface $authenticationHandler
    ) {
        $this->entryRepository = $entryRepository;
        $this->contractRepository = $contractRepository;
        $this->authenticationHandler = $authenticationHandler;
    }


    /**
     * @param string $name
     * @param ViewRequest $request
     * @return ViewResponse
     * @throws \Exception
     */
    public function createView(string $name, ViewRequest $request): ViewResponse
    {

        if (!\array_key_exists('start', $request->getParameters())) {
            $start = date('Y-m-01');
        } else {
            $start = $request->getParameters()['start'];
        }
        if (!\array_key_exists('end', $request->getParameters())) {
            $end = date('Y-m-t');
        } else {
            $end = $request->getParameters()['end'];
        }

        $user = $this->authenticationHandler->getUser();
        $entries = $this->entryRepository->findWithinInterval($start, $end, $user);
        $contracts = $this->contractRepository->findWithinInterval($start, $end, $user);

        $result = [
            'meta' => [
                'totalIncome' => 0,
                'totalExpenses' => 0,
            ],
            'graphData' => [
                'income' => [],
                'expenses' => [],
            ],
        ];

        /** @var Entry $entry */
        foreach ($entries as $entry) {
            $categoryName = $entry->getCategory() !== null ? $entry->getCategory()->getName() : 'undefined';
            $result['entries'][] = [
                'name' => $entry->getName(),
                'amount' => $entry->getAmount(),
                'billingDate' => $entry->getDate()->format('Y-m-d'),
                'category' => $categoryName,
            ];

            $this->calculateGraphData((float)$entry->getAmount(), $result, $categoryName);
        }

        /** @var Contract $contract */
        foreach ($contracts as $contract) {
            $categoryName = $contract->getCategory() !== null ? $contract->getCategory()->getName() : 'undefined';
            $rule = new Rule($contract->getIntervalRule());
            $rule->setStartDate($contract->getStartDate());

            $contractEndDate = $contract->getEndDate();
            if ($contractEndDate !== null) {
                $rule->setEndDate($contractEndDate);
            }

            $transformer = new  ArrayTransformer();
            $occurrences = $transformer->transform($rule)->startsBetween(new \DateTime($start), new \DateTime($end), true);

            foreach ($occurrences as $occurrence) {
                if ($contractEndDate !== null && $occurrence->getStart() > $contractEndDate) {
                    continue;
                }
                $result['contracts'][] = [
                    'name' => $contract->getName(),
                    'amount' => $contract->getAmount(),
                    'billingDate' => $occurrence->getStart()->format('Y-m-d'),
                    'interval' => $contract->getDueInterval(),
                    'category' => $categoryName,
                ];

                $this->calculateGraphData((float)$contract->getAmount(), $result, $categoryName);
            }
        }

        $result['meta']['calculatedBalance'] = $result['meta']['totalIncome'] - $result['meta']['totalExpenses'];

        return $this->createResponse(
            $request,
            $result
        );
    }

    /**
     * @param float $amount
     * @param array $resultData
     * @param string $categoryName
     */
    private function calculateGraphData(float $amount, array &$resultData, string $categoryName): void
    {
        if ($amount > 0) {
            if (!\array_key_exists($categoryName, $resultData['graphData']['income'])) {
                $resultData['graphData']['income'][$categoryName] = 0;
            }
            $resultData['graphData']['income'][$categoryName] += $amount;
            $resultData['meta']['totalIncome'] += $amount;
        } else {
            $amount = (float)abs($amount);
            if (!\array_key_exists($categoryName, $resultData['graphData']['expenses'])) {
                $resultData['graphData']['expenses'][$categoryName] = 0;
            }
            $resultData['graphData']['expenses'][$categoryName] += $amount;
            $resultData['meta']['totalExpenses'] += $amount;
        }
    }

}
