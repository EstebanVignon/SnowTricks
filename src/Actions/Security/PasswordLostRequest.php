<?php

declare(strict_types=1);

namespace App\Actions\Security;

use App\Entity\TokenHistory;
use App\Form\Security\PasswordLostRequestType;
use App\Repository\TokenHistoryRepository;
use App\Repository\UserRepository;
use App\Responders\ViewResponder;
use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Security;

class PasswordLostRequest
{
    /**
     * @var FormFactoryInterface
     */
    private FormFactoryInterface $formFactory;

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

    /**
     * @var UserRepository
     */
    private UserRepository $userRepository;

    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $em;

    /**
     * @var MailerInterface
     */
    private MailerInterface $mailer;

    /**
     * @var TokenHistoryRepository
     */
    private TokenHistoryRepository $tokenHistoryRepository;

    public function __construct(
        FormFactoryInterface $formFactory,
        Security $security,
        UrlGeneratorInterface $urlGenerator,
        FlashBagInterface $flash,
        UserRepository $userRepository,
        EntityManagerInterface $em,
        MailerInterface $mailer,
        TokenHistoryRepository $tokenHistoryRepository
    ) {
        $this->formFactory = $formFactory;
        $this->security = $security;
        $this->urlGenerator = $urlGenerator;
        $this->flash = $flash;
        $this->userRepository = $userRepository;
        $this->em = $em;
        $this->mailer = $mailer;
        $this->tokenHistoryRepository = $tokenHistoryRepository;
    }

    /**
     * @param ViewResponder $responder
     * @param Request $request
     * @Route("request-new-password", name="security_password_lost_request")
     */
    public function __invoke(ViewResponder $responder, Request $request)
    {
        //REDIRECT IF CONNECTED
        if ($this->security->getUser()) {
            $this->flash->add('warning', 'Vous êtes déjà connecté');
            $url = $this->urlGenerator->generate('homepage');
            return new RedirectResponse($url);
        }

        $form = $this->formFactory->createBuilder(PasswordLostRequestType::class)->getForm()->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $dto = $form->getData();
            $user = $this->userRepository->findOneBy(['username' => $dto->username]);

            if ($user) {
                //DELETE OLD PASSWORD TOKENS
                $oldPwdTokens = $this->tokenHistoryRepository->findBy(['user' => $user]);
                foreach ($oldPwdTokens as $token) {
                    $this->em->remove($token);
                }

                //TOKEN
                $token = new TokenHistory();
                $token->setType('password-reset')
                    ->setValue(Uuid::uuid4()->toString())
                    ->setUser($user);

                //PERSIST & FLUSH
                $this->em->persist($token);
                $this->em->flush();

                //SEND EMAIL
                $email = new TemplatedEmail();
                $email->from(new Address("contact@snowtricks.com"))
                    ->to($user->getEmail())
                    ->htmlTemplate('emails/password_reset_token.html.twig')
                    ->context([
                        'token' => $token->getValue()
                    ])
                    ->subject("Réinitialiser votre mot de passe SnowTricks");
                $this->mailer->send($email);
            }

            //FLASH MESSAGE AND REDIRECTION IN ALL CASES
            $this->flash->add(
                'success',
                "Si l'identifiant donné existe, un email de réinitialisation de mot de passe à bien
                     été envoyé sur la boite mail associée"
            );
            $url = $this->urlGenerator->generate('homepage');
            return new RedirectResponse($url);
        }

        return $responder('security/password_lost_request.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
