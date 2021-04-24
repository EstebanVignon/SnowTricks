<?php

declare(strict_types=1);

namespace App\Actions\Security;

use App\Form\LoginType;
use App\Responders\ViewResponder;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class Login
{
    /**
     * @var FormFactoryInterface
     */
    private FormFactoryInterface $formFactory;

    /**
     * @var AuthenticationUtils
     */
    private AuthenticationUtils $authenticationUtils;

    /**
     * @var Security
     */
    private Security $security;

    /**
     * @var UrlGeneratorInterface
     */
    private UrlGeneratorInterface $urlGenerator;

    /**
     * @var FlashBagInterface
     */
    private FlashBagInterface $flash;

    public function __construct(
        FormFactoryInterface $formFactory,
        AuthenticationUtils $authenticationUtils,
        Security $security,
        UrlGeneratorInterface $urlGenerator,
        FlashBagInterface $flash
    ) {
        $this->formFactory = $formFactory;
        $this->authenticationUtils = $authenticationUtils;
        $this->security = $security;
        $this->urlGenerator = $urlGenerator;
        $this->flash = $flash;
    }

    /**
     * @param ViewResponder $responder
     * @param Request $request
     * @Route("login", name="security_login")
     */
    public function __invoke(ViewResponder $responder, Request $request)
    {
        if ($this->security->getUser()) {
            $this->flash->add('warning', 'Vous êtes déjà connecté');
            $url = $this->urlGenerator->generate('homepage');
            return new RedirectResponse($url);
        }

        $form = $this->formFactory->createBuilder(LoginType::class, [
            'username' => $this->authenticationUtils->getLastUsername()
        ])->getForm()->handleRequest($request);

        return $responder('security/login.html.twig', [
            'form' => $form->createView(),
            'error' => $this->authenticationUtils->getLastAuthenticationError()
        ]);
    }
}
