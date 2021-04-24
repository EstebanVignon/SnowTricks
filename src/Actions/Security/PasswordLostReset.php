<?php

declare(strict_types=1);

namespace App\Actions\Security;

use App\Form\Security\PasswordLostResetType;
use App\Repository\TokenHistoryRepository;
use App\Repository\UserRepository;
use App\Responders\ViewResponder;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class PasswordLostReset
{
    /**
     * @var FlashBagInterface
     */
    private FlashBagInterface $flash;

    /**
     * @var UrlGeneratorInterface
     */
    private UrlGeneratorInterface $urlGenerator;

    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    /**
     * @var TokenHistoryRepository
     */
    private TokenHistoryRepository $tokenHistoryRepository;

    /**
     * @var FormFactoryInterface
     */
    private FormFactoryInterface $formFactory;

    /**
     * @var UserRepository
     */
    private UserRepository $userRepository;

    /**
     * @var UserPasswordEncoderInterface
     */
    private UserPasswordEncoderInterface $encode;

    public function __construct(
        FlashBagInterface $flash,
        UrlGeneratorInterface $urlGenerator,
        EntityManagerInterface $entityManager,
        TokenHistoryRepository $tokenHistoryRepository,
        FormFactoryInterface $formFactory,
        UserRepository $userRepository,
        UserPasswordEncoderInterface $encode
    ) {
        $this->flash = $flash;
        $this->urlGenerator = $urlGenerator;
        $this->entityManager = $entityManager;
        $this->tokenHistoryRepository = $tokenHistoryRepository;
        $this->formFactory = $formFactory;
        $this->userRepository = $userRepository;
        $this->encode = $encode;
    }

    /**
     * @param $token
     * @param ViewResponder $responder
     * @param Request $request
     * @Route("reset-password/{token}", name="security_password_lost_reset")
     */
    public function __invoke($token, ViewResponder $responder, Request $request)
    {
        $tokenObj = $this->tokenHistoryRepository->findOneBy(['value' => $token]);

        if ($tokenObj !== null) {
            $form = $this->formFactory->createBuilder(PasswordLostResetType::class)->getForm()->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $user = $this->userRepository->findOneBy(['username' => $tokenObj->getUser()->getUsername()]);
                $passwordResetDTO = $form->getData();
                $encorePassword = $this->encode->encodePassword($user, $passwordResetDTO->password);
                $user->setPassword($encorePassword);

                $this->entityManager->remove($tokenObj);
                $this->entityManager->persist($user);
                $this->entityManager->flush();

                $this->flash->add('success', 'Mot de passe mis à jour');
                $url = $this->urlGenerator->generate('security_login');
                return new RedirectResponse($url);
            }

            return $responder('security/password_lost_reset.html.twig', [
                'form' => $form->createView()
            ]);
        } else {
            $this->flash->add('fail', 'Token de reinitalisation de mot de passe invalide');
            $url = $this->urlGenerator->generate('homepage');
            return new RedirectResponse($url);
        }
    }
}
