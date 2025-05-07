<?php

namespace App\DataFixtures;

use App\Entity\Theme;
use App\Entity\Cursus;
use App\Entity\Lesson;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        // 1. Themes with associated metadata and curricula
        $themesMeta = [
            'Musique' => [
                'text' => 'Apprenez à maîtriser un instrument grâce à nos cours experts.',
                'image' => 'music.webp',
                'icon' => 'fa-music',
                'cursus' => [
                    ['Cursus d’initiation à la guitare', 50, [
                        ['Découverte de l’instrument', 26],
                        ['Les accords et les gammes', 26],
                    ]],
                    ['Cursus d’initiation au piano', 50, [
                        ['Découverte de l’instrument', 26],
                        ['Les accords et les gammes', 26],
                    ]],
                ],
            ],
            'Informatique' => [
                'text' => 'Développez vos compétences numériques et maîtrisez les outils technologiques de demain.',
                'image' => 'it.webp',
                'icon' => 'fa-laptop-code',
                'cursus' => [
                    ['Cursus d’initiation au développement web', 60, [
                        ['Les langages HTML et CSS', 32],
                        ['Dynamiser votre site avec Javascript', 32],
                    ]],
                ],
            ],
            'Jardinage' => [
                'text' => 'Apprenez à cultiver votre propre jardin et à entretenir vos espaces verts avec passion.',
                'image' => 'gardening.webp',
                'icon' => 'fa-seedling',
                'cursus' => [
                    ['Cursus d’initiation au jardinage', 30, [
                        ['Les outils du jardinier', 16],
                        ['Jardiner avec la lune', 16],
                    ]],
                ],
            ],
            'Cuisine' => [
                'text' => 'Découvrez l’art culinaire et réalisez des recettes savoureuses, de l\'entrée au dessert.',
                'image' => 'kitchen.webp',
                'icon' => 'fa-utensils',
                'cursus' => [
                    ['Cursus d’initiation à la cuisine', 44, [
                        ['Les modes de cuisson', 23],
                        ['Les saveurs', 23],
                    ]],
                    ['Cursus d’initiation au dressage culinaire', 48, [
                        ['Mettre en œuvre le style dans l’assiette', 26],
                        ['Harmoniser un repas à quatre plats', 26],
                    ]],
                ],
            ],
        ];

        // 2. Global list of 12 videos (1 per lesson)
        $videoUrls = [
            "https://youtu.be/HQXK2KnXouo?si=RT6WQUvLdHX9ZO1M",
            "https://youtu.be/KZWNXGKh8lY?si=h91bdIFO7gSYE8yz",
            "https://youtu.be/Jq8patW7QXY?si=PvPGAwXmpxrRek1s",
            "https://youtu.be/2y9t5yzIwJg?si=sH02seZQHLhN-PNV",
            "https://youtu.be/64X4ZJ-F7EI?si=e68zUa8QIrJxISWb",
            "https://youtu.be/v3Ho7QVaTXM?si=u6TaxJinm3dUt-ey",
            "https://youtu.be/tKOgEv6ZzNc?si=Iyqt3e7YmeJUnk6z",
            "https://youtu.be/9t1VAmoiZBU?si=MAxidHJ-6V5dGlQV",
            "https://youtu.be/4L_PUwUbKhs?si=MJAL7Of3NFnZur0g",
            "https://youtu.be/e4Gx_G0U2Mk?si=ELYAeqoFnyKNFyUf",
            "https://youtu.be/YlH1aOOvEdc?si=9PVehzWuypb_gje6",
            "https://youtu.be/m9U1mr-VbxM?si=hP7SVazN7_IeTG7g",
        ];
        $videoGlobalIndex = 0;

        // 3. Main creation loop
        foreach ($themesMeta as $title => $meta) {
            $theme = new Theme();
            $theme->setTitle($title)
                  ->setText($meta['text'])
                  ->setImage($meta['image'])
                  ->setIcon($meta['icon']);
            $manager->persist($theme);

            foreach ($meta['cursus'] as [$cTitle, $cPrice, $lessons]) {
                $cursus = new Cursus();
                $cursus->setTitle($cTitle)
                       ->setPrice($cPrice)
                       ->setTheme($theme)
                       ->setCreatedAt(new \DateTimeImmutable());
                $manager->persist($cursus);

                foreach ($lessons as [$lTitle, $lPrice]) {
                    $lesson = new Lesson();
                    $lesson->setTitle($lTitle)
                           ->setContent("Lorem ipsum dolor sit amet, consectetur adipiscing elit.")
                           ->setPrice($lPrice)
                           ->setCursus($cursus)
                           ->setCreatedAt(new \DateTimeImmutable());

                    // Single video attribution
                    if (isset($videoUrls[$videoGlobalIndex])) {
                        $lesson->setVideoUrl($videoUrls[$videoGlobalIndex]);
                        $videoGlobalIndex++;
                    }

                    $manager->persist($lesson);
                }
            }
        }

        // 4. Creating test users
        $admin = new User();
        $admin->setEmail('admin@example.com')
              ->setUsername('admin')
              ->setRoles(['ROLE_ADMIN'])
              ->setPassword($this->passwordHasher->hashPassword($admin, 'password'))
              ->setActivated(true);
        $manager->persist($admin);

        $student = new User();
        $student->setEmail('student@example.com')
                ->setUsername('student')
                ->setRoles(['ROLE_USER'])
                ->setPassword($this->passwordHasher->hashPassword($student, 'password'))
                ->setActivated(true);
        $manager->persist($student);

        $manager->flush();
    }
}