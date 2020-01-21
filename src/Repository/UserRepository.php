<?php

namespace App\Repository;

use App\Entity\Role;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * @param $batchId
     *
     * @return array
     */
    public function getUsersByBatchId($batchId): array
    {
        return $this->createQueryBuilder('users')
            ->select('users', 'users_roles', 'users_batches')
            ->join('users.batches', 'users_batches')
            ->andWhere('users_batches.id = :batchId')
            ->setParameter('batchId', $batchId)
            ->leftJoin('users.roles', 'users_roles')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param integer $batchId
     *
     * @return array
     */
    public function getUsersAndRoleByBatchId($batchId): array
    {
        $usersObjects = $this->getUsersByBatchId($batchId);
        $usersAndRole = [];
        /** @var User $userObject */
        foreach ($usersObjects as $key => $userObject) {
            $usersAndRole[$key]['id'] = $userObject->getId();
            $usersAndRole[$key]['name'] = sprintf('%s %s', $userObject->getName(), $userObject->getSurname());
            /** @var Role $role */
            $usersAndRole[$key]['role'] = null;
            foreach ($userObject->getRoles() as $role) {
                if ($role->getBatch()->getId() === $batchId) {
                    $usersAndRole[$key]['role'] = $role->getName();
                }
            }
        }
        $keys = array_column($usersAndRole, 'role');
        array_multisort($keys, SORT_DESC, $usersAndRole);

        return $usersAndRole;
    }
    /*
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
