<?php

namespace App\Repository;

use App\Entity\Genre;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Genre|null find($id, $lockMode = null, $lockVersion = null)
 * @method Genre|null findOneBy(array $criteria, array $orderBy = null)
 * @method Genre[]    findAll()
 * @method Genre[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GenreRepository extends ServiceEntityRepository
{
    /**
     * GenreRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Genre::class);
    }

    /** @noinspection PhpUnhandledExceptionInspection
     * @noinspection PhpUnhandledExceptionInspection
     */
    /**
     * @param Genre $entity
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function remove(Genre $entity):void
    {
        $this->_em->remove($entity);
        $this->_em->flush();
    }

    /** @noinspection PhpUnhandledExceptionInspection
     * @noinspection PhpUnhandledExceptionInspection
     */
    /**
     * @param Genre $entity
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function save(Genre $entity):void
    {
        $this->_em->persist($entity);
        $this->_em->flush();
    }
}
