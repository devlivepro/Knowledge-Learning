<?php

namespace App\Tests\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class SecurityControllerTest extends WebTestCase
{
    public function testLoginPageIsAccessible(): void
    {
        $client  = static::createClient();
        $crawler = $client->request('GET', '/login');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h2', 'Connexion');
    }

    public function testRedirectIfAlreadyLoggedIn(): void
    {
        // 1) Créez d’abord le client
        $client = static::createClient();

        // 2) Récupérez le container à partir du client
        $container = $client->getContainer();
        $em        = $container->get('doctrine')->getManager();
        $hasher    = $container->get(UserPasswordHasherInterface::class);

        // 3) Générez des identifiants uniques pour éviter les doublons
        $suffix   = uniqid();
        $username = 'user_'.$suffix;
        $email    = 'user_'.$suffix.'@example.com';

        // 4) Créez et persistez l’utilisateur
        $user = new User();
        $user->setUsername($username);
        $user->setEmail($email);
        $user->setRoles(['ROLE_USER']);
        $user->setActivated(true);
        $user->setPassword($hasher->hashPassword($user, 'testpassword'));

        $em->persist($user);
        $em->flush();

        // 5) Connectez-le dans le client
        $client->loginUser($user);

        // 6) Tentez d’accéder à /login
        $client->request('GET', '/login');

        // 7) Vérifiez la redirection vers /student
        $this->assertResponseRedirects('/student');
    }
}