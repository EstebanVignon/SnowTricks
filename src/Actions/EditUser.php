<?php

declare(strict_types=1);

namespace App\Actions;

use App\Form\Security\EditUserType;
use App\Responders\ViewResponder;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Security;

class EditUser
{
    /**
     * @var FormFactoryInterface
     */
    private FormFactoryInterface $formFactory;

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

    /**
     * @var Filesystem
     */
    private Filesystem $filesystem;

    /**
     * @var Security
     */
    private Security $security;

    public function __construct(
        FormFactoryInterface $formFactory,
        EntityManagerInterface $em,
        UrlGeneratorInterface $urlGenerator,
        FlashBagInterface $flash,
        ContainerBagInterface $params,
        Filesystem $filesystem,
        Security $security
    ) {
        $this->formFactory = $formFactory;
        $this->em = $em;
        $this->urlGenerator = $urlGenerator;
        $this->flash = $flash;
        $this->params = $params;
        $this->filesystem = $filesystem;
        $this->security = $security;
    }

    /**
     * @Route("/profil/edit", name="edit_profil")
     * @param ViewResponder $responder
     * @param Request $request
     * @return Response
     */
    public function __invoke(ViewResponder $responder, Request $request): Response
    {
        if (!$this->security->getUser()) {
            $this->flash->add('warning', "Connectez-vous pour éditer votre profil");
            $url = $this->urlGenerator->generate('security_login');
            return new RedirectResponse($url);
        }

        $user = $this->security->getUser();


        $form = $this->formFactory->createBuilder(EditUserType::class)->getForm()->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $mainPicture = $form->get('avatar')->getData();

            if ($mainPicture) {
                if ($user->getAvatar()) {
                    $oldFile = $this->params->get('avatar_directory') . '/' . $user->getAvatar();
                    $this->filesystem->remove([$oldFile]);
                }
                $file = md5(uniqid()) . '.' . $mainPicture->guessExtension();
                $mainPicture->move(
                    $this->params->get('avatar_directory'),
                    $file
                );
                $user->setAvatar($file);
            }

            $this->em->persist($user);
            $this->em->flush();

            $this->flash->add('success', 'Profil mis à jour');

            $url = $this->urlGenerator->generate('homepage');
            return new RedirectResponse($url);
        }

        return $responder('user/edit.html.twig', [
            'form' => $form->createView(),
            'user' => $user
        ]);
    }
}
