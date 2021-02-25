<?php

declare(strict_types=1);

namespace App\Actions;

use App\Entity\Trick;
use App\Repository\TrickRepository;
use App\Responders\ViewResponder;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class Home
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var TrickRepository
     */
    private $repository;

    public function __construct(
        EntityManagerInterface $em,
        TrickRepository $repository
    )
    {
        $this->em = $em;
        $this->repository = $repository;
    }

    /**
     * @param ViewResponder $responder
     * @return Response
     * @Route("/", name="homepage")
     */
    public function __invoke(ViewResponder $responder): Response
    {
        $tricks = $this->repository->findAll();

//        $trick = $this->repository->findOneBy(['title' => 'New Trick 3']);
//        $this->em->remove($trick);
//        $this->em->flush();

//        $trick = new Trick();
//        $trick->create('Trick3', 'trick-3', 'Description', null, null);
//        $this->em->persist($trick);
//        $this->em->flush();

        return $responder('homepage.html.Twig', ['tricks' => $tricks]);
    }
}


