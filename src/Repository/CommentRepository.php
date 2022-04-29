<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Comment;
use App\Entity\Post;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Security;

/**
 * @method Comment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Comment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Comment[]    findAll()
 * @method Comment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class CommentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, Security $security)
    {
        parent::__construct($registry, Comment::class);
        $this->user = $security->getUser();
    }

    public function findValidate(Post $post): Array
    {
       /*  $qb = $this->createQueryBuilder('comment')
                ->andWhere('comment.post = :post')
                ->setParameter('post', $post); */

        $qb = $this->createQueryBuilder('comment')
            ->join('comment.post', 'p')
            ->andWhere('p.name = :id')
            ->setParameter('id', $post->getId());

        if (null === $this->user) {
            $qb->andWhere('comment.validated = 1');
        }

        return $qb->getQuery()->getResult();
    }
}
