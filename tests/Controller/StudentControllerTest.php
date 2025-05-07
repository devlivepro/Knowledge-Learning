<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Repository\UserRepository;
use App\Repository\CursusRepository;
use App\Entity\User;

class StudentControllerTest extends WebTestCase
{
    private function loginAsTestUser($client): void
    {
        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('student@example.com');

        $client->loginUser($testUser);
    }

    public function testDashboardAccess(): void
    {
        $client = static::createClient();
        $this->loginAsTestUser($client);

        $client->request('GET', '/student');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Bienvenue');
    }

    public function testCoursesAccess(): void
    {
        $client = static::createClient();
        $this->loginAsTestUser($client);

        $client->request('GET', '/student/courses');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Formations');
    }

    public function testShopPage(): void
    {
        $client = static::createClient();
        $this->loginAsTestUser($client);

        $client->request('GET', '/student/shop');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Boutique de formations');
    }

    public function testBuyCursusRedirects(): void
    {
        $client = static::createClient();
        $this->loginAsTestUser($client);

        $cursusRepository = static::getContainer()->get(CursusRepository::class);
        $cursus = $cursusRepository->findOneBy([]);

        if ($cursus) {
            $client->request('GET', '/cursus/' . $cursus->getId() . '/buy');
            $this->assertResponseStatusCodeSame(302); // Stripe redirection
        } else {
            $this->markTestIncomplete('Aucun cursus trouvé dans la base de données.');
        }
    }

    public function testInvoiceAccess(): void
    {
        $client = static::createClient();
        $this->loginAsTestUser($client);

        $client->request('GET', '/student/invoice');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Mes achats');
    }
}