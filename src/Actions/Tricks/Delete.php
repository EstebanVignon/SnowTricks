<?php

declare(strict_types=1);

namespace App\Actions\Tricks;

use App\Helpers\PicturesFilesystemHelper;
use App\Repository\CategoryRepository;
use App\Repository\TrickRepository;
use App\Responders\ViewResponder;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class Delete
{
    /**
     * @var CategoryRepository
     */
    private CategoryRepository $categoryRepository;

    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $em;

    /**
     * @var TrickRepository
     */
    private TrickRepository $trickRepository;

    /**
     * @var UrlGeneratorInterface
     */
    private UrlGeneratorInterface $urlGenerator;

    /**
     * @var FlashBagInterface
     */
    private FlashBagInterface $flash;

    /**
     * @var PicturesFilesystemHelper
     */
    private PicturesFilesystemHelper $picturesFilesystemHelper;

    /**
     * @var Filesystem
     */
    private Filesystem $filesystem;

    /**
     * @var ContainerBagInterface
     */
    private ContainerBagInterface $params;

    public function __construct(
        CategoryRepository $categoryRepository,
        EntityManagerInterface $em,
        TrickRepository $trickRepository,
        UrlGeneratorInterface $urlGenerator,
        FlashBagInterface $flash,
        PicturesFilesystemHelper $picturesFilesystemHelper,
        Filesystem $filesystem,
        ContainerBagInterface $params
    ) {
        $this->categoryRepository = $categoryRepository;
        $this->em = $em;
        $this->trickRepository = $trickRepository;
        $this->urlGenerator = $urlGenerator;
        $this->flash = $flash;
        $this->picturesFilesystemHelper = $picturesFilesystemHelper;
        $this->filesystem = $filesystem;
        $this->params = $params;
    }

    /**
     * @Route("/trick/delete/{slug}", name="delete_trick")
     * @param string $slug
     * @param ViewResponder $responder
     * @param Request $request
     * @return Response
     */
    public function __invoke(string $slug, ViewResponder $responder, Request $request): Response
    {
        $trick = $this->trickRepository->findOneBy(['slug' => $slug]);

        if (!$trick) {
            throw new NotFoundHttpException("Trick not found");
        }

        if ($request->query->get('confirmation') === "yes") {
            //Pictures
            foreach ($trick->getPictures() as $picture) {
                $this->picturesFilesystemHelper->deleteTrick(
                    $picture->getId(),
                    'tricks_pictures_directory'
                );
            }
            //Main picture
            if ($trick->getMainPicture() !== "default.jpg") {
                $this->filesystem->remove(
                    $this->params->get("main_picture_directory") . '/' . $trick->getMainPicture()
                );
            }
            //Comments
            foreach ($trick->getComments() as $comment) {
                $this->em->remove($comment);
            }

            //Trick
            $this->em->remove($trick);
            $this->em->flush();

            $this->flash->add('success', 'Le trick a bien été supprimé');
            $url = $this->urlGenerator->generate('homepage');
            return new RedirectResponse($url);
        } else {
            return $responder('trick/delete.html.twig', [
                'trick' => $trick
            ]);
        }
    }
}
