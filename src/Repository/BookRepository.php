<?php

namespace App\Repository;

use App\Entity\Book;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Book|null find($id, $lockMode = null, $lockVersion = null)
 * @method Book|null findOneBy(array $criteria, array $orderBy = null)
 * @method Book[]    findAll()
 * @method Book[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookRepository extends ServiceEntityRepository
{
    /**
     * BookRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class);
    }

    /**
     * @param string|null $genre
     *
     * @return Query
     */
    public function queryByGenreLike(?string $genre): Query
    {
        if (null === $genre) {
            return $this->createQueryBuilder('b')
                ->addSelect('b.created_at')
                ->getQuery();
        }

        return $this->createQueryBuilder('b')
            ->join('b.genres', 'g')
            ->andWhere('g.name LIKE :val')
            ->setParameter('val', '%'.$genre.'%')
            ->getQuery()
        ;
    }

    /** @noinspection PhpUnhandledExceptionInspection
     * @noinspection PhpUnhandledExceptionInspection
     */
    /**
     * @param Book $entity
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function remove(Book $entity):void
    {
        $this->_em->remove($entity);
        $this->_em->flush();
    }

    /** @noinspection PhpUnhandledExceptionInspection
     * @noinspection PhpUnhandledExceptionInspection
     */
    /**
     * @param Book $entity
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function save(Book $entity):void
    {
        $this->_em->persist($entity);
        $this->_em->flush();
    }
}
