<?php

namespace App\Doctrine\Repository;

use App\Doctrine\Entity\Contract;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Contract|null find($id, $lockMode = null, $lockVersion = null)
 * @method Contract|null findOneBy(array $criteria, array $orderBy = null)
 * @method Contract[]    findAll()
 * @method Contract[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContractRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Contract::class);
    }

    public function save(Contract $contract): Contract
    {
        $this->_em->persist($contract);
        $this->_em->flush();

        return $contract;
    }

    public function findWithinInterval($start, $end, $user)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.startDate >= :start')
            ->andWhere('e.endDate <= :end')
            ->orWhere('e.endDate is null')
            ->andWhere('e.user = :user')
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }
}
