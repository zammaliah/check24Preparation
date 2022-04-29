<?php

declare(strict_types=1);

namespace App\Controller\Blog;

use App\Entity\Post;
use App\Entity\Rate;
use App\Entity\Vote;
use App\Form\RateType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/post/vote')]
final class RateController extends AbstractController
{
    #[Route('/{id}', name: 'post_vote', methods: ["POST"] )]
    public function action(Post $post, Request $request): Response
    {
        $rate = $post->getRate()?? new Rate($post);
        $form = $this->createForm(RateType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userVote = $form->get('value')->getData();
            $vote = new Vote($rate, $userVote);
            $numberOfVotes = $rate->getNumberOfVotes() + 1;
            $rating = $this->calculateRating($rate->getRating(), $rate->getNumberOfVotes(), $userVote);
            $rate->update($rating, $numberOfVotes);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($rate);
            $entityManager->persist($vote);
            $entityManager->flush();
        }

        return $this->redirectToRoute('post_detail', ["slug" => $post->slug()]);
    }

    private function calculateRating(Float $oldRating, Int $oldNumVote, Int $userVote) : float
    {
        $oldSum = $oldRating * $oldNumVote;
        $rating = ($oldSum + $userVote) / ($oldNumVote + 1);
        return $rating;
    }

}
