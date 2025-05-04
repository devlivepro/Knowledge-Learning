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
        // 1. Theme metadata (text, image, icon)
        $themesMeta = [
            'Musique' => [
                'text'  => 'Apprenez à maîtriser un instrument grâce à nos cours experts.',
                'image' => 'music.webp',
                'icon'  => 'fa-music',
                'cursus'=> [
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
                'text'  => 'Développez vos compétences numériques et maîtrisez les outils technologiques de demain.',
                'image' => 'it.webp',
                'icon'  => 'fa-laptop-code',
                'cursus'=> [
                    ['Cursus d’initiation au développement web', 60, [
                        ['Les langages HTML et CSS', 32],
                        ['Dynamiser votre site avec Javascript', 32],
                    ]],
                ],
            ],
            'Jardinage' => [
                'text'  => 'Apprenez à cultiver votre propre jardin et à entretenir vos espaces verts avec passion.',
                'image' => 'gardening.webp',
                'icon'  => 'fa-seedling',
                'cursus'=> [
                    ['Cursus d’initiation au jardinage', 30, [
                        ['Les outils du jardinier', 16],
                        ['Jardiner avec la lune', 16],
                    ]],
                ],
            ],
            'Cuisine' => [
                'text'  => 'Découvrez l’art culinaire et réalisez des recettes savoureuses, de l\'entrée au dessert.',
                'image' => 'kitchen.webp',
                'icon'  => 'fa-utensils',
                'cursus'=> [
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

        // 2. Creating themes + curricula + lessons
        foreach ($themesMeta as $title => $meta) {
            $theme = new Theme();
            $theme->setTitle($title)
                  ->setText($meta['text'])
                  ->setImage($meta['image'])
                  ->setIcon($meta['icon'])
            ;
            $manager->persist($theme);

            foreach ($meta['cursus'] as [$cTitle, $cPrice, $lessons]) {
                $cursus = new Cursus();
                $cursus->setTitle($cTitle)
                       ->setPrice($cPrice)
                       ->setTheme($theme)
                ;
                $manager->persist($cursus);

                foreach ($lessons as [$lTitle, $lPrice]) {
                    $lesson = new Lesson();
                    $lesson->setTitle($lTitle)
                           ->setContent('Contenu de la leçon : ' . $lTitle)
                           ->setVideoUrl('https://example.com/video.mp4')
                           ->setPrice($lPrice)
                           ->setCursus($cursus)
                    ;
                    $manager->persist($lesson);
                }
            }
        }

        // 3. User creation
        $admin = new User();
        $admin->setEmail('admin@example.com')
              ->setUsername('admin')
              ->setRoles(['ROLE_ADMIN'])
              ->setPassword($this->passwordHasher->hashPassword($admin, 'password'))
              ->setActivated(true)
        ;
        $manager->persist($admin);

        $student = new User();
        $student->setEmail('student@example.com')
                ->setUsername('student')
                ->setRoles(['ROLE_USER'])
                ->setPassword($this->passwordHasher->hashPassword($student, 'password'))
                ->setActivated(true)
        ;
        $manager->persist($student);

        $manager->flush();
    }
}