<?php

declare(strict_types=1);

namespace App\Controller\Blog;

use App\Form\CommentType;
use App\Form\RateType;
use App\Repository\CommentRepository;
use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

final class DetailController extends AbstractController
{
    #[Route('/detail/{slug}', name: 'post_detail')]
    public function action(
        PostRepository $repository,
        CommentRepository $commentRepository,
        String $slug
    ): Response {
        $post = $repository->detail($slug) ?: throw new NotFoundHttpException();

        $comment = $this->createForm(
            CommentType::class,
            options: ['action' => $this->generateUrl('post_comment', ["id" => $post->getId()])]
        );

        $vote = $this->createForm(
            RateType::class,
            options: ['action' => $this->generateUrl('post_vote', ["id" => $post->getId()])]
        );

        $comments = $commentRepository->findValidate($post);

        return $this->render(
            'post/show.html.twig',
            [
                'post' => $post,
                'comments' => $comments,
                'comment' => $comment->createView(),
                'vote' => $vote->createView()
            ]
        );
    }
}
