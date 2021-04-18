<?php

declare(strict_types=1);

namespace App\Actions\Security;

use App\Entity\TokenHistory;
use App\Form\Security\RegistrationType;
use App\Responders\ViewResponder;
use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
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

    /**
     * @var MailerInterface
     */
    private MailerInterface $mailer;

    public function __construct(
        FormFactoryInterface $formFactory,
        AuthenticationUtils $authenticationUtils,
        EntityManagerInterface $em,
        FlashBagInterface $flash,
        UrlGeneratorInterface $urlGenerator,
        UserPasswordEncoderInterface $encoder,
        MailerInterface $mailer
    )
    {
        $this->formFactory = $formFactory;
        $this->authenticationUtils = $authenticationUtils;
        $this->em = $em;
        $this->flash = $flash;
        $this->urlGenerator = $urlGenerator;
        $this->encoder = $encoder;
        $this->mailer = $mailer;
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

            //PASSWORD
            $hash = $this->encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($hash);

            //TOKEN
            $token = new TokenHistory();
            $token->setType('registration')
                ->setValue(Uuid::uuid4()->toString())
                ->setUser($user);

            //PERSIST & FLUSH
            $this->em->persist($user);
            $this->em->persist($token);
            $this->em->flush();

            //SEND EMAIL
            $email = new TemplatedEmail();
            $email->from(new Address("contact@snowtricks.com"))
                ->to($user->getEmail())
                ->htmlTemplate('emails/registration_token.html.twig')
                ->context([
                    'token' => $token->getValue()
                ])
                ->subject("Validez votre compte Snowtricks");
            $this->mailer->send($email);

            //FLASH MESSAGE AND REDIRECTION
            $this->flash->add('success', 'Compte créé mais en attente de validation, allez voir votre boite mail');
            $url = $this->urlGenerator->generate('security_login');
            return new RedirectResponse($url);
        }

        return $responder('security/registration.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
