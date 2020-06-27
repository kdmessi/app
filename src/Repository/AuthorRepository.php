<?php

namespace App\Repository;

use App\Entity\Author;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Author|null find($id, $lockMode = null, $lockVersion = null)
 * @method Author|null findOneBy(array $criteria, array $orderBy = null)
 * @method Author[]    findAll()
 * @method Author[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AuthorRepository extends ServiceEntityRepository
{
    /**
     * AuthorRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Author::class);
    }

    /** @noinspection PhpUnhandledExceptionInspection
     * @noinspection PhpUnhandledExceptionInspection
     */
    /**
     * @param Author $entity
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function remove(Author $entity):void
    {
        $this->_em->remove($entity);
        $this->_em->flush();
    }

    /** @noinspection PhpUnhandledExceptionInspection
     * @noinspection PhpUnhandledExceptionInspection
     */
    /**
     * @param Author $entity
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function save(Author $entity):void
    {
        $this->_em->persist($entity);
        $this->_em->flush();
    }
}
