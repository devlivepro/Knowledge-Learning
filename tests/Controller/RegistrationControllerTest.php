<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RegistrationControllerTest extends WebTestCase
{
    public function testRegisterPageLoadsCorrectly(): void
    {
        $client  = static::createClient();
        $crawler = $client->request('GET', '/register');

        // on remplit le formulaire avec des valeurs uniques pour ne pas violer les clés uniques
        $username = 'user_'.uniqid();
        $email    = 'user_'.uniqid().'@example.com';
        $password = 'azerty123'; // doit respecter la longueur mini si tu as un  Length(min=6)

        $form = $crawler->selectButton("S'inscrire")->form([
            'registration_form[username]'      => $username,
            'registration_form[email]'         => $email,
            'registration_form[plainPassword]' => $password,
        ]);

        $client->submit($form);

        // on vérifie qu'on est redirigé vers la page de connexion
        $this->assertResponseRedirects('/login');
    }
}
