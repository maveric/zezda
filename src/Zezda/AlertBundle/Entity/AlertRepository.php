<?php

namespace Zezda\AlertBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * AlertRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class AlertRepository extends EntityRepository
{

	public function getLastSmsAlert()
	{
		$qb = $this->createQueryBuilder('b')
			->andWhere('b.type = 1')
			->addOrderBy('b.datetime', 'DESC')
			->setMaxResults(1);

		return $qb->getQuery()->getResult();
	}
}