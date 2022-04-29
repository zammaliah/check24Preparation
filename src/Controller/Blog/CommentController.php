<?php

declare(strict_types=1);

namespace App\Controller\Blog;

use App\Entity\Comment;
use App\Entity\Post;
use App\Form\CommentType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use DateTimeImmutable;

#[Route('/post/comment')]
final class CommentController extends AbstractController
{
    #[Route('/{id}', name: 'post_comment', methods: ["POST"] )]
    public function action(Post $post, Request $request): Response
    {
        $comment = new Comment();
        $comment->setPost($post);
        $comment->setDate(new DateTimeImmutable());
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($comment);
            $entityManager->flush();
        }

        return $this->redirectToRoute('post_detail', ["slug" => $post->slug()]);
    }

    #[Route('/{post}/{id}', name: 'post_comment_delete', methods: ['DELETE'])]
    public function delete(Request $request, Post $post, Comment $comment): Response
    {
        if ($this->isCsrfTokenValid('delete' . $comment->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($comment);
            $entityManager->flush();
        }

        return $this->redirectToRoute('post_detail', ["slug" => $post->slug()]);
    }

    #[Route('/validate/{post}/{id}', name: 'post_comment_validate', methods: ['POST'])]
    public function validate(Request $request, Post $post, Comment $comment): Response
    {
        if ($this->isCsrfTokenValid('validate' . $comment->getId(), $request->request->get('_token'))) {

            $comment->setValidated(true);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($comment);
            $entityManager->flush();
        }

        return $this->redirectToRoute('post_detail', ["slug" => $post->slug()]);
    }
}
