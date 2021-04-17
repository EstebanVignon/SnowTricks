<?php

declare(strict_types=1);

namespace App\Actions\Security;

use App\Form\LoginType;
use App\Form\Security\RegistrationType;
use App\Responders\ViewResponder;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

final class Registration
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
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $em;

    /**
     * @var FlashBagInterface
     */
    private FlashBagInterface $flash;

    /**
     * @var UrlGeneratorInterface
     */
    private UrlGeneratorInterface $urlGenerator;
    /**
     * @var UserPasswordEncoderInterface
     */
    private UserPasswordEncoderInterface $encoder;

    public function __construct(
        FormFactoryInterface $formFactory,
        AuthenticationUtils $authenticationUtils,
        EntityManagerInterface $em,
        FlashBagInterface $flash,
        UrlGeneratorInterface $urlGenerator,
        UserPasswordEncoderInterface $encoder
    )
    {
        $this->formFactory = $formFactory;
        $this->authenticationUtils = $authenticationUtils;
        $this->em = $em;
        $this->flash = $flash;
        $this->urlGenerator = $urlGenerator;
        $this->encoder = $encoder;
    }

    /**
     * @param ViewResponder $responder
     * @param Request $request
     * @Route("registration", name="security_registration")
     */
    public function __invoke(ViewResponder $responder, Request $request)
    {
        $form = $this->formFactory->createBuilder(RegistrationType::class)->getForm()->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $user->setIsActive(false);

            $hash = $this->encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($hash);

            $this->em->persist($user);
            $this->em->flush();

            //  TODO : Generate token and send email

            $this->flash->add('success', 'Compte créé mais en attente de validation, allez voir votre boite mail');

            $url = $this->urlGenerator->generate('security_login');
            return new RedirectResponse($url);
        }

        return $responder('security/registration.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
