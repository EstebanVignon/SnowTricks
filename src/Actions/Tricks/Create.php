<?php

declare(strict_types=1);

namespace App\Actions\Tricks;

use App\Form\Trick\TrickCreateType;
use App\Repository\CategoryRepository;
use App\Responders\ViewResponder;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class Create
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

    public function __construct(
        FormFactoryInterface $formFactory,
        CategoryRepository $categoryRepository,
        EntityManagerInterface $em,
        UrlGeneratorInterface $urlGenerator,
        FlashBagInterface $flash,
        ContainerBagInterface $params
    )
    {
        $this->formFactory = $formFactory;
        $this->categoryRepository = $categoryRepository;
        $this->em = $em;
        $this->urlGenerator = $urlGenerator;
        $this->flash = $flash;
        $this->params = $params;
    }

    /**
     * @Route("/trick/create", name="create_trick")
     * @param ViewResponder $responder
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function __invoke(ViewResponder $responder, Request $request)
    {
        $form = $this->formFactory->createBuilder(TrickCreateType::class)->getForm()->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $trick = $form->getData();

            //PICTURES
            $pictures = $form->get('pictures')->getData();

            if ($pictures) {
                foreach ($pictures as $picture) {
                    $file = $picture->getFile();
                    $newFilename = md5(uniqid()) . '.' . $file->guessExtension();
                    try {
                        $file->move(
                            $this->params->get('tricks_pictures_directory'),
                            $newFilename
                        );
                    } catch (FileException $e) {
                        throw new FileException($e);
                    }

                    $picture->setFileName($newFilename);
                    $this->em->persist($picture);
                }
            }

            //File MainPicture Upload
            $mainPicture = $form->get('mainPicture')->getData();
            if ($mainPicture) {
                $file = md5(uniqid()) . '.' . $mainPicture->guessExtension();
                $mainPicture->move(
                    $this->params->get('main_picture_directory'),
                    $file
                );
                $trick->setMainPicture($file);
            }

            //Videos
            foreach ($trick->getVideos() as $video) {
                $this->em->persist($video);
            }

            //Persist & Flush
            $this->em->persist($trick);
            $this->em->flush();

            $this->flash->add('success', 'Le trick a bien été créé');

            $url = $this->urlGenerator->generate('show_trick', ['slug' => $trick->getSlug()]);
            return new RedirectResponse($url);
        }

        return $responder('trick/create.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
