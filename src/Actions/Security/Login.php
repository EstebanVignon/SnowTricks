<?php

declare(strict_types=1);

namespace App\Actions\Security;

use App\Form\LoginType;
use App\Form\Trick\TrickCreateType;
use App\Responders\ViewResponder;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

final class Login
{
    /**
     * @var FormFactoryInterface
     */
    private FormFactoryInterface $formFactory;
    /**
     * @var AuthenticationUtils
     */
    private AuthenticationUtils $authenticationUtils;

    public function __construct(
        FormFactoryInterface $formFactory,
        AuthenticationUtils $authenticationUtils
    )
    {
        $this->formFactory = $formFactory;
        $this->authenticationUtils = $authenticationUtils;
    }

    /**
     * @param ViewResponder $responder
     * @Route("login", name="security_login")
     */
    public function __invoke(ViewResponder $responder, Request $request)
    {
        $form = $this->formFactory->createBuilder(LoginType::class, [
            'username' => $this->authenticationUtils->getLastUsername()
        ])->getForm()->handleRequest($request);

        return $responder('security/login.html.twig', [
            'form' => $form->createView(),
            'error' => $this->authenticationUtils->getLastAuthenticationError()
        ]);
    }
}