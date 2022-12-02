<?php

namespace Plugin\RemoteIslandsDeliveryFee\Repository;

use Doctrine\ORM\EntityRepository;

class RemoteIslandsDeliveryFeeRepository extends EntityRepository
{
    
    public function getPref($value){
        return $this-> createQueryBuilder('p')
            ->innerJoin('p.Pref', 'pp')
            ->where('pp.id = ?1')
            ->setParameter(1,$value)
            ->getQuery()
            ->getResult();
    }
    public function getMaxId(){
        return $this-> createQueryBuilder('p')
            ->orderBy('p.id', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
