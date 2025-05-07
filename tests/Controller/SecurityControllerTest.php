<?php

namespace App\Tests\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class SecurityControllerTest extends WebTestCase
{
    /**
     * Test that the login page is publicly accessible
     * and displays the expected heading.
     */
    public function testLoginPageIsAccessible(): void
    {
        // Create a new HTTP client for the test
        $client  = static::createClient();
        // Request the login page
        $crawler = $client->request('GET', '/login');

        // Assert that the response status is 200 OK
        $this->assertResponseIsSuccessful();
        // Assert that an <h2> element contains the text "Connexion"
        $this->assertSelectorTextContains('h2', 'Connexion');
    }

    /**
     * Test that an already authenticated user cannot revisit
     * the login page and is redirected to their dashboard.
     */
    public function testRedirectIfAlreadyLoggedIn(): void
    {
        // 1) Instantiate the HTTP client
        $client = static::createClient();

        // 2) Fetch the service container from the client
        $container = $client->getContainer();
        // Get the Doctrine entity manager
        $em        = $container->get('doctrine')->getManager();
        // Get the password hasher to encode our test user's password
        $hasher    = $container->get(UserPasswordHasherInterface::class);

        // 3) Generate a unique suffix to avoid duplicate entries
        $suffix   = uniqid();
        $username = 'user_' . $suffix;
        $email    = 'user_' . $suffix . '@example.com';

        // 4) Create a new User entity and persist it
        $user = new User();
        $user->setUsername($username);
        $user->setEmail($email);
        $user->setRoles(['ROLE_USER']);         // Give them the ROLE_USER
        $user->setActivated(true);              // Mark the account as activated
        // Hash and set the password so loginUser() will accept it
        $user->setPassword($hasher->hashPassword($user, 'testpassword'));

        $em->persist($user);
        $em->flush();

        // 5) Log in the newly created user for the test client
        $client->loginUser($user);

        // 6) Attempt to access the login page again
        $client->request('GET', '/login');

        // 7) Assert that we are redirected to the student dashboard
        $this->assertResponseRedirects('/student');
    }
}