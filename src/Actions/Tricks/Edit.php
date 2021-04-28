<?php

declare(strict_types=1);

namespace App\Actions\Tricks;

use App\Entity\Picture;
use App\Form\Trick\TrickEditType;
use App\Helpers\PicturesFilesystemHelper;
use App\Repository\CategoryRepository;
use App\Repository\PictureRepository;
use App\Repository\TrickRepository;
use App\Responders\ViewResponder;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Security;

class Edit
{
    /**
     * @var FormFactoryInterface
     */
    private FormFactoryInterface $formFactory;

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
     * @var ContainerBagInterface
     */
    private ContainerBagInterface $params;

    /**
     * @var Filesystem
     */
    private Filesystem $filesystem;

    /**
     * @var PictureRepository
     */
    private PictureRepository $pictureRepository;

    /**
     * @var PicturesFilesystemHelper
     */
    private PicturesFilesystemHelper $picturesFilesystemHelper;
    /**
     * @var Security
     */
    private Security $security;

    public function __construct(
        FormFactoryInterface $formFactory,
        CategoryRepository $categoryRepository,
        EntityManagerInterface $em,
        TrickRepository $trickRepository,
        UrlGeneratorInterface $urlGenerator,
        FlashBagInterface $flash,
        ContainerBagInterface $params,
        Filesystem $filesystem,
        PictureRepository $pictureRepository,
        PicturesFilesystemHelper $picturesFilesystemHelper,
        Security $security
    ) {
        $this->formFactory = $formFactory;
        $this->categoryRepository = $categoryRepository;
        $this->em = $em;
        $this->trickRepository = $trickRepository;
        $this->urlGenerator = $urlGenerator;
        $this->flash = $flash;
        $this->params = $params;
        $this->filesystem = $filesystem;
        $this->pictureRepository = $pictureRepository;
        $this->picturesFilesystemHelper = $picturesFilesystemHelper;
        $this->security = $security;
    }

    /**
     * @Route("/trick/edit/{slug}", name="edit_trick")
     * @param string $slug
     * @param ViewResponder $responder
     * @param Request $request
     * @return Response
     */
    public function __invoke(string $slug, ViewResponder $responder, Request $request): Response
    {
        if (!$this->security->getUser()) {
            $this->flash->add('warning', "Connectez-vous pour éditer ce trick");
            $url = $this->urlGenerator->generate('security_login');
            return new RedirectResponse($url);
        }

        $trick = $this->trickRepository->findOneBy(['slug' => $slug]);

        $oldPictures = array_map(function (Picture $picture) {
            return $picture->getId();
        }, $trick->getPictures()->toArray());

        if (!$trick) {
            throw new NotFoundHttpException("Trick not found");
        }

        $form = $this->formFactory->createBuilder(TrickEditType::class, $trick)->getForm()->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newTrick = $form->getData();

            //PICTURES

            //Pictures array filter add / delete / edit
            $pictures = $form->get('pictures')->getData();
            $picturesArray = array_map(function (Picture $picture) {
                return $picture->getId();
            }, $trick->getPictures()->toArray());
            $idToDelete = array_diff($oldPictures, $picturesArray);
            $idToAdd = array_diff($picturesArray, $oldPictures);
            $idToUpdate = array_intersect($oldPictures, $picturesArray);

//            dump('all', $picturesArray);
//            dump('delete', $idToDelete);
//            dump('add', $idToAdd);
//            dump('update', $idToUpdate);
//
//            exit();

            //Delete pictures
            if ($idToDelete) {
                foreach ($idToDelete as $id) {
                    $this->picturesFilesystemHelper->deleteTrick(
                        $id,
                        'tricks_pictures_directory'
                    );
                }
            }

            foreach ($pictures as $picture) {
                //Add new pictures
                if (in_array($picture->getId(), $idToAdd)) {
                    $this->picturesFilesystemHelper->createPicture(
                        $picture,
                        'tricks_pictures_directory',
                        $trick
                    );
                }
                //Edit pictures
                if (in_array($picture->getId(), $idToUpdate)) {
                    if ($picture->getFile() !== null) {
                        $this->picturesFilesystemHelper->deleteTrick(
                            $picture->getId(),
                            'tricks_pictures_directory'
                        );
                        $this->picturesFilesystemHelper->createPicture(
                            $picture,
                            'tricks_pictures_directory',
                            $trick
                        );
                    }
                }
            }

            //VIDEOS

            foreach ($newTrick->getVideos() as $video) {
                $video->setTrick($newTrick);
                $this->em->persist($video);
            }

            // MAIN IMAGE

            $mainPicture = $form->get('mainPicture')->getData();
            if ($mainPicture) {
                if ($trick->getMainPicture() !== $trick::DEFAULT_IMAGE) {
                    $oldFile = $this->params->get('main_picture_directory') . '/' . $trick->getMainPicture();
                    $this->filesystem->remove([$oldFile]);
                }
                $file = md5(uniqid()) . '.' . $mainPicture->guessExtension();
                $mainPicture->move(
                    $this->params->get('main_picture_directory'),
                    $file
                );
                $trick->setMainPicture($file);
            }

            $trick->setUpdatedAt(new \DateTime("now"));

            $this->em->persist($newTrick);
            $this->em->flush();

            $this->flash->add('success', 'Le trick a bien été modifié');

            $url = $this->urlGenerator->generate('show_trick', ['slug' => $trick->getSlug()]);
            return new RedirectResponse($url);
        }

        return $responder('trick/edit.html.twig', [
            'form' => $form->createView(),
            'trick' => $trick
        ]);
    }
}
