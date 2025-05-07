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
    // Track whether the test database schema has already been created
    private static bool $schemaInitialized = false;

    private KernelBrowser $client;
    private EntityManagerInterface $em;
    private UserPasswordHasherInterface $hasher;

    /**
     * Set up a fresh client, entity manager, and password hasher before each test.
     * Also (once) drop and recreate the database schema in the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Create the HTTP client for browser-style testing
        $this->client = static::createClient();
        $container     = $this->client->getContainer();

        // Fetch the Doctrine entity manager from the test container
        $this->em     = $container->get(EntityManagerInterface::class);
        // Fetch the password hasher service for encoding test user passwords
        $this->hasher = $container->get(UserPasswordHasherInterface::class);

        // Initialize (drop & re-create) the schema only once across all tests
        if (!self::$schemaInitialized) {
            $tool     = new SchemaTool($this->em);
            $metadata = $this->em->getMetadataFactory()->getAllMetadata();

            // Remove existing tables and rebuild them from entity metadata
            $tool->dropSchema($metadata);
            $tool->createSchema($metadata);

            self::$schemaInitialized = true;
        }
    }

    /**
     * Create and persist a new User entity with a unique email/username.
     * Password is hashed so that loginUser() will accept it.
     *
     * @return User The newly created test user
     */
    private function createTestUser(): User
    {
        // Use a unique suffix to avoid collisions in subsequent tests
        $suffix = uniqid();

        $user = new User();
        $user->setUsername('user_' . $suffix);
        $user->setEmail('user_' . $suffix . '@example.com');

        // Hash the dummy password before setting it on the user
        $hashed = $this->hasher->hashPassword($user, 'dummy_password');
        $user->setPassword($hashed);

        $user->setRoles(['ROLE_USER']);
        $user->setActivated(true);
        $user->setVerificationToken(null);
        $user->setCreatedAt(new \DateTimeImmutable());

        // Persist and flush to save the user in the test database
        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }

    /**
     * Verify that unauthenticated requests to the student courses page
     * are redirected to the login page.
     */
    public function testRedirectWhenNotAuthenticated(): void
    {
        // Request the protected page without logging in
        $this->client->request('GET', '/student/courses');

        // Expect a redirect to /login
        $this->assertResponseRedirects('/login');
    }

    /**
     * Ensure that a logged-in user can access the student courses page
     * and that the appropriate heading element exists.
     */
    public function testCoursesPageIsAccessible(): void
    {
        // Create a fresh test user and log in
        $user = $this->createTestUser();
        $this->client->loginUser($user);

        // Request the courses page
        $crawler = $this->client->request('GET', '/student/courses');

        // Assert HTTP 200 OK
        $this->assertResponseIsSuccessful();

        // Check that an <h1> or <h2> with the class 'text-center' is present
        $this->assertSelectorExists('h1.text-center, h2.text-center');
    }

    /**
     * Verify that, when no purchases exist, the page shows the
     * “You have not purchased any courses yet” message.
     */
    public function testProgressDataIsPresent(): void
    {
        // Create and log in a new test user
        $user = $this->createTestUser();
        $this->client->loginUser($user);

        // Request the courses page
        $crawler = $this->client->request('GET', '/student/courses');

        // Assert the page displays the no-purchases message
        $this->assertSelectorTextContains(
            'p',
            "Vous n'avez encore acheté aucune formation."
        );
    }
}