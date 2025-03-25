<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<User>
 *
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

    public function createUser(string $token, int $id, string $username, string $displayName, string $profile, string $picture): void
    {
        $user = new User();

        $user->setRobloxToken($token);
        $user->setRobloxId($id);
        $user->setUsername($username);
        $user->setDisplayName($displayName);
        $user->setProfile($profile);
        $user->setPicture($picture);

        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    public function findByToken(string $token): ?User
    {
        return $this->findOneBy(['robloxToken' => $token]);
    }

    public function findById(int $id): ?User
    {
        return $this->findOneBy(['robloxId' => $id]);
    }
}
