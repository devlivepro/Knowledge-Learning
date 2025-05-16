<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RegistrationControllerTest extends WebTestCase
{
    /**
     * Test that the registration page loads,
     * allows form submission with valid data,
     * and redirects to the login page.
     */
    public function testRegisterPageLoadsCorrectly(): void
    {
        // 1) Create a new HTTP client for this test
        $client  = static::createClient();
        // 2) Request the registration page
        $crawler = $client->request('GET', '/register');

        // 3) Generate unique credentials to avoid unique-key collisions
        $username = 'user_' . uniqid();
        $email    = 'user_' . uniqid() . '@example.com';
        // Ensure the password meets any minimum length requirements
        $password = '7e]2Azvx7aCAG):';

        // 4) Select the registration form by its submit button label
        $form = $crawler->selectButton("S'inscrire")->form([
            // Map your form field names to the test values
            'registration_form[username]'      => $username,
            'registration_form[email]'         => $email,
            'registration_form[plainPassword]' => $password,
        ]);

        // 5) Submit the form
        $client->submit($form);

        // 6) Assert that after submission, we are redirected to the login page
        $this->assertResponseRedirects('/login');
    }
}
