<?php

namespace App\Tests\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class StudentCoursesControllerTest extends WebTestCase
{
    private static bool $schemaInitialized = false;

    private KernelBrowser $client;
    private EntityManagerInterface $em;
    private UserPasswordHasherInterface $hasher;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = static::createClient();
        $container     = $this->client->getContainer();

        $this->em     = $container->get(EntityManagerInterface::class);
        $this->hasher = $container->get(UserPasswordHasherInterface::class);

        if (!self::$schemaInitialized) {
            $tool     = new SchemaTool($this->em);
            $metadata = $this->em->getMetadataFactory()->getAllMetadata();

            $tool->dropSchema($metadata);
            $tool->createSchema($metadata);

            self::$schemaInitialized = true;
        }
    }

    private function createTestUser(): User
    {
        $suffix = uniqid();

        $user = new User();
        $user->setUsername('user_' . $suffix);
        $user->setEmail('user_' . $suffix . '@example.com');

        $hashed = $this->hasher->hashPassword($user, 'dummy_password');
        $user->setPassword($hashed);

        $user->setRoles(['ROLE_USER']);
        $user->setActivated(true);
        $user->setVerificationToken(null);
        $user->setCreatedAt(new \DateTimeImmutable());

        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }

    public function testRedirectWhenNotAuthenticated(): void
    {
        $this->client->request('GET', '/student/courses');
        $this->assertResponseRedirects('/login');
    }

    public function testCoursesPageIsAccessible(): void
    {
        $user = $this->createTestUser();
        $this->client->loginUser($user);

        $crawler = $this->client->request('GET', '/student/courses');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('h1.text-center');
    }

    public function testProgressDataIsPresent(): void
    {
        $user = $this->createTestUser();
        $this->client->loginUser($user);

        $crawler = $this->client->request('GET', '/student/courses');

        // Si pas d'achats, on affiche un message adapté
        $this->assertSelectorTextContains('p', "Vous n'avez encore acheté aucune formation.");
    }
}