<?php

namespace App\Repository;

use App\Entity\Comment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Comment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Comment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Comment[]    findAll()
 * @method Comment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentRepository extends ServiceEntityRepository
{
    /**
     * CommentRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comment::class);
    }

    /** @noinspection PhpUnhandledExceptionInspection
     * @noinspection PhpUnhandledExceptionInspection
     */
    /**
     * @param Comment $entity
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function remove(Comment $entity):void
    {
        $this->_em->remove($entity);
        $this->_em->flush();
    }

    /** @noinspection PhpUnhandledExceptionInspection
     * @noinspection PhpUnhandledExceptionInspection
     */
    /**
     * @param Comment $entity
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function save(Comment $entity):void
    {
        $this->_em->persist($entity);
        $this->_em->flush();
    }
}
