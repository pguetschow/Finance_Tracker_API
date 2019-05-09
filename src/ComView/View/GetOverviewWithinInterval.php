<?php
declare(strict_types=1);


namespace App\ComView\View;

use App\Entity\Contract;
use App\Entity\Entry;
use App\Helper\AuthenticationAwareHelper;
use App\Repository\ContractRepository;
use App\Repository\EntryRepository;
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
     * @var AuthenticationAwareHelper
     */
    private $protectedAware;

    /**
     * @param EntryRepository $entryRepository
     * @param ContractRepository $contractRepository
     * @param AuthenticationAwareHelper $protectedAware
     */
    public function __construct(EntryRepository $entryRepository, ContractRepository $contractRepository, AuthenticationAwareHelper $protectedAware)
    {
        $this->entryRepository = $entryRepository;
        $this->contractRepository = $contractRepository;
        $this->protectedAware = $protectedAware;
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

        $user = $this->protectedAware->getUser();
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

            if ($contract->getEndDate() !== null) {
               $rule->setEndDate($contract->getEndDate());
            }

            $transformer = new  ArrayTransformer();
            $occurrences = $transformer->transform($rule)->startsBetween(new \DateTime($start), new \DateTime($end), true);

            foreach ($occurrences as $occurrence) {
                if ($contract->getEndDate() !== null && $occurrence->getStart() > $contract->getEndDate()) {
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
