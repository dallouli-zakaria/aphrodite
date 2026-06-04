<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

final class AuthController extends AbstractController
{
    #[Route('/connexion', name: 'app_login', methods: ['GET', 'POST'])]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('account_home');
        }

        return $this->render('Auth/login.html.twig', [
            'last_username' => $authenticationUtils->getLastUsername(),
            'error' => $authenticationUtils->getLastAuthenticationError(),
        ]);
    }

    #[Route('/inscription', name: 'app_register', methods: ['GET', 'POST'])]
    public function register(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher,
    ): Response {
        if ($this->getUser()) {
            return $this->redirectToRoute('account_home');
        }

        $errors = [];
        if ($request->isMethod('POST')) {
            $fullName = trim((string) $request->request->get('fullName'));
            $email = strtolower(trim((string) $request->request->get('email')));
            $password = (string) $request->request->get('password');

            if ($fullName === '') {
                $errors[] = 'Le nom complet est obligatoire.';
            }
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Email invalide.';
            }
            if (strlen($password) < 8) {
                $errors[] = 'Le mot de passe doit contenir au moins 8 caracteres.';
            }
            if ($entityManager->getRepository(User::class)->findOneBy(['email' => $email])) {
                $errors[] = 'Un compte existe deja avec cet email.';
            }

            if ($errors === []) {
                $user = (new User())
                    ->setFullName($fullName)
                    ->setEmail($email)
                    ->setRoles(['ROLE_USER']);
                $user->setPassword($passwordHasher->hashPassword($user, $password));

                $entityManager->persist($user);
                $entityManager->flush();

                $this->addFlash('success', 'Compte cree. Vous pouvez vous connecter.');

                return $this->redirectToRoute('app_login');
            }
        }

        return $this->render('Auth/register.html.twig', [
            'errors' => $errors,
        ]);
    }

    #[Route('/connexion/google', name: 'app_google_login', methods: ['GET'])]
    public function googleLogin(): Response
    {
        $this->addFlash('warning', 'Connexion Google preparee. Il faut ajouter les identifiants OAuth Google pour l activer.');

        return $this->redirectToRoute('app_login');
    }

    #[Route('/account', name: 'account_home', methods: ['GET'])]
    public function account(EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $orders = $user instanceof User
            ? $entityManager->getRepository(Order::class)->findBy(['user' => $user], ['createdAt' => 'DESC'])
            : [];

        return $this->render('Auth/account.html.twig', [
            'orders' => $orders,
        ]);
    }

    #[Route('/logout', name: 'app_logout', methods: ['GET'])]
    public function logout(): void
    {
        throw new \LogicException('Symfony intercepts this route for logout.');
    }
}
