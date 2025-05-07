<?php

namespace App\Tests\Controller;

use App\Entity\Cursus;
use App\Entity\Theme;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class StudentControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $em;
    private UserPasswordHasherInterface $hasher;

    protected function setUp(): void
    {
        parent::setUp();

        // Initialize the client and required services
        $this->client = static::createClient();
        $container     = static::getContainer();
        $this->em      = $container->get(EntityManagerInterface::class);
        $this->hasher  = $container->get(UserPasswordHasherInterface::class);
    }

    /**
     * Helper: find or create a test user and log in.
     */
    private function loginAsTestUser(KernelBrowser $client): void
    {
        /** @var UserRepository $userRepo */
        $userRepo = static::getContainer()->get(UserRepository::class);

        $user = $userRepo->findOneByEmail('student@example.com');
        if (!$user) {
            $user = new User();
            $user->setUsername('student');
            $user->setEmail('student@example.com');
            $user->setRoles(['ROLE_USER']);
            $user->setActivated(true);
            $user->setCreatedAt(new \DateTimeImmutable());
            // Hash a dummy password (not actually checked by loginUser)
            $user->setPassword($this->hasher->hashPassword($user, 'password'));

            $this->em->persist($user);
            $this->em->flush();
        }

        $client->loginUser($user);
    }

    /**
     * Helper: create and persist a minimal Theme.
     */
    private function createTestTheme(): Theme
    {
        $theme = new Theme();
        // Use the actual setter from your entity:
        $theme->setTitle('Test Theme');

        // If your Theme entity has non-nullable fields, set them here:
        // e.g. $theme->setImage('placeholder.jpg');
        //      $theme->setIcon('icon-name');
        //      $theme->setSlug('test-theme');
        
        $theme->setCreatedAt(new \DateTimeImmutable());

        $this->em->persist($theme);
        $this->em->flush();

        return $theme;
    }

    /**
     * Helper: create and persist a Cursus tied to a Theme
     */
    private function createTestCursus(): Cursus
    {
        $theme = $this->createTestTheme();

        $cursus = new Cursus();
        $cursus->setTitle('Test Cursus');       // Title setter on Cursus
        $cursus->setPrice(42);                  // Price setter
        $cursus->setCreatedAt(new \DateTimeImmutable());
        $cursus->setTheme($theme);              // Associate with a valid Theme

        $this->em->persist($cursus);
        $this->em->flush();

        return $cursus;
    }

    public function testDashboardAccess(): void
    {
        $this->loginAsTestUser($this->client);

        $this->client->request('GET', '/student');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Bienvenue');
    }

    public function testCoursesAccess(): void
    {
        $this->loginAsTestUser($this->client);

        $this->client->request('GET', '/student/courses');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Formations');
    }

    public function testShopPage(): void
    {
        $this->loginAsTestUser($this->client);

        $this->client->request('GET', '/student/shop');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Boutique de formations');
    }

    public function testBuyCursusRedirects(): void
    {
        $this->loginAsTestUser($this->client);

        // Ensure a Cursus exists (with a valid Theme) for the buy route
        $cursus = $this->createTestCursus();

        $this->client->request('GET', '/cursus/' . $cursus->getId() . '/buy');
        $this->assertResponseStatusCodeSame(302); // expecting a redirect (e.g. Stripe)
    }

    public function testInvoiceAccess(): void
    {
        $this->loginAsTestUser($this->client);

        $this->client->request('GET', '/student/invoice');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Mes achats');
    }
}