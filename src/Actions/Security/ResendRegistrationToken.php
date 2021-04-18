<?php

declare(strict_types=1);

namespace App\Actions\Security;

use App\Entity\TokenHistory;
use App\Repository\TokenHistoryRepository;
use App\Repository\UserRepository;
use App\Responders\ViewResponder;
use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class ResendRegistrationToken
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
     * @var UserRepository
     */
    private UserRepository $userRepository;

    /**
     * @var MailerInterface
     */
    private MailerInterface $mailer;

    public function __construct(
        FlashBagInterface $flash,
        UrlGeneratorInterface $urlGenerator,
        EntityManagerInterface $entityManager,
        TokenHistoryRepository $tokenHistoryRepository,
        UserRepository $userRepository,
        MailerInterface $mailer
    )
    {
        $this->flash = $flash;
        $this->urlGenerator = $urlGenerator;
        $this->entityManager = $entityManager;
        $this->tokenHistoryRepository = $tokenHistoryRepository;
        $this->userRepository = $userRepository;
        $this->mailer = $mailer;
    }

    /**
     * @param $username
     * @param Request $request
     * @Route("resend-registration-token/{username}", name="security_resend_registration_token")
     */
    public function __invoke($username, Request $request)
    {
        $user = $this->userRepository->findOneBy(['username' => $username]);

        $tokens = $this->tokenHistoryRepository->findBy([
            'user' => $user->getId(),
            'type' => 'registration'
        ]);

        if ($tokens) {
            foreach ($tokens as $token) {
                $this->entityManager->remove($token);
            }
        }

        $newToken = new TokenHistory();
        $newToken->setType('registration')
            ->setValue(Uuid::uuid4()->toString())
            ->setUser($user);

        $this->entityManager->persist($newToken);

        $this->entityManager->flush();

        //SEND EMAIL
        $email = new TemplatedEmail();
        $email->from(new Address("contact@snowtricks.com"))
            ->to($user->getEmail())
            ->htmlTemplate('emails/registration_token.html.twig')
            ->context([
                'token' => $newToken->getValue()
            ])
            ->subject("Nouveau lien pour valider votre compte Snowtricks");
        $this->mailer->send($email);

        //FLASH MESSAGE AND REDIRECTION
        $this->flash->add('success', 'Un nouveau lien de validation à bien été envoyé sur votre boite mail');
        $url = $this->urlGenerator->generate('security_login');
        return new RedirectResponse($url);
    }
}