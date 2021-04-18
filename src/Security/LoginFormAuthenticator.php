<?php

namespace App\Security;

use App\Repository\TokenHistoryRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;

class LoginFormAuthenticator extends AbstractFormLoginAuthenticator
{
    /**
     * @var UserPasswordEncoderInterface
     */
    protected UserPasswordEncoderInterface $encoder;

    /**
     * @var UrlGeneratorInterface
     */
    private UrlGeneratorInterface $urlGenerator;

    /**
     * @var FlashBagInterface
     */
    private FlashBagInterface $flashBag;

    /**
     * @var UserRepository
     */
    private UserRepository $userRepository;

    /**
     * @var TokenHistoryRepository
     */
    private TokenHistoryRepository $tokenHistoryRepository;

    public function __construct(
        UserPasswordEncoderInterface $encoder,
        UrlGeneratorInterface $urlGenerator,
        FlashBagInterface $flashBag,
        UserRepository $userRepository,
        TokenHistoryRepository $tokenHistoryRepository
    )
    {
        $this->encoder = $encoder;
        $this->urlGenerator = $urlGenerator;
        $this->flashBag = $flashBag;
        $this->userRepository = $userRepository;
        $this->tokenHistoryRepository = $tokenHistoryRepository;
    }

    public function supports(Request $request): bool
    {
        return $request->attributes->get('_route') === 'security_login'
            && $request->isMethod('POST');
    }

    public function getCredentials(Request $request)
    {
        return $request->request->get('login');
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $user = $userProvider->loadUserByUsername($credentials['username']);

        if (!$user) {
            throw new CustomUserMessageAuthenticationException('Identifiants invalides');
        }
        return $user;
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        $isValid = $this->encoder->isPasswordValid($user, $credentials['password']);

        if (!$isValid) {
            throw new CustomUserMessageAuthenticationException('Identifiants invalides');
        }

        if (!$user->getIsActive()) {
            $url = $this->urlGenerator->generate('security_resend_registration_token', [
                'username' => $credentials['username']
            ]);

            $tokens = $this->tokenHistoryRepository->findBy([
                'user' => $user->getId(),
                'type' => 'registration'
            ]);

            if (count($tokens) === 0) {
                $this->flashBag->add('warning-no-token', [
                    "message" => "Votre lien de validation à périmé, cliquez sur le bouton ci-dessous pour le renvoyer sur votre boite mail",
                    "link" => $url
                ]);
            } else {
                $this->flashBag->add('warning-token', [
                    "message" => "Votre compte n'est pas actif, vérifiez votre boite mail",
                    "link" => $url
                ]);
            }

            throw new CustomUserMessageAuthenticationException("Compte non activé");
        }
        return true;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $providerKey): Response
    {
        return new RedirectResponse('/');
    }

    protected function getLoginUrl(): string
    {
        return $this->urlGenerator->generate('security_login');
    }
}
