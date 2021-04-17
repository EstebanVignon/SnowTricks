<?php

declare(strict_types=1);

namespace App\Actions\Security;

use App\Repository\TokenHistoryRepository;
use App\Responders\ViewResponder;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class RegistrationTokenValidation
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

    public function __construct(
        FlashBagInterface $flash,
        UrlGeneratorInterface $urlGenerator,
        EntityManagerInterface $entityManager,
        TokenHistoryRepository $tokenHistoryRepository
    )
    {
        $this->flash = $flash;
        $this->urlGenerator = $urlGenerator;
        $this->entityManager = $entityManager;
        $this->tokenHistoryRepository = $tokenHistoryRepository;
    }

    /**
     * @param $token
     * @param ViewResponder $responder
     * @param Request $request
     * @Route("registration-token-validation/{token}", name="security_registration_token_validation")
     */
    public function __invoke($token, ViewResponder $responder, Request $request)
    {
        $tokenObj = $this->tokenHistoryRepository->findOneBy(['value' => $token]);
        if ($tokenObj !== null) {
            $user = $tokenObj->getUser();
            $user->setIsActive(true);

            $this->entityManager->remove($tokenObj);
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            $this->flash->add('success', 'Votre compte à bien été activé !');
            $url = $this->urlGenerator->generate('security_login');
            return new RedirectResponse($url);
        } else {
            $this->flash->add('fail', 'Token invalide');
            $url = $this->urlGenerator->generate('security_login');
            return new RedirectResponse($url);
        }
    }
}
