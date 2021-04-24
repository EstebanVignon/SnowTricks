<?php

declare(strict_types=1);

namespace App\Actions\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class LogoutMessage
{
    /**
     * @var FlashBagInterface
     */
    private FlashBagInterface $flash;

    /**
     * @var UrlGeneratorInterface
     */
    private UrlGeneratorInterface $urlGenerator;

    public function __construct(
        FlashBagInterface $flash,
        UrlGeneratorInterface $urlGenerator
    ) {

        $this->flash = $flash;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @Route("/logout_message", name="logout_message")
     */
    public function __invoke()
    {
        $this->flash->add('success', "Vous êtes bien déconnecté");
        $url = $this->urlGenerator->generate('homepage');
        return new RedirectResponse($url);
    }
}
