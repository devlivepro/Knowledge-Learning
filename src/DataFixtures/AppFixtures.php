<?php

namespace App\DataFixtures;

use App\Entity\Theme;
use App\Entity\Cursus;
use App\Entity\Leçon;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Create Themes and their corresponding Cursus and Lessons

        // Theme: Music
        $musicTheme = new Theme();
        $musicTheme->setName('Music');
        $manager->persist($musicTheme);

        // Cursus: Guitar Basics
        $guitarCursus = new Cursus();
        $guitarCursus->setTitle('Guitar Basics');
        $guitarCursus->setPrice(50); // Price as per the specifications
        $guitarCursus->setTheme($musicTheme);
        $manager->persist($guitarCursus);

        // Leçons for Guitar Basics
        $this->createLesson($manager, $guitarCursus, 'Découverte de l’instrument', 26);
        $this->createLesson($manager, $guitarCursus, 'Les accords et les gammes', 26);

        // Cursus: Piano Basics
        $pianoCursus = new Cursus();
        $pianoCursus->setTitle('Piano Basics');
        $pianoCursus->setPrice(50); // Price as per the specifications
        $pianoCursus->setTheme($musicTheme);
        $manager->persist($pianoCursus);

        // Leçons for Piano Basics
        $this->createLesson($manager, $pianoCursus, 'Découverte de l’instrument', 26);
        $this->createLesson($manager, $pianoCursus, 'Les accords et les gammes', 26);

        // Theme: Informatics
        $informaticsTheme = new Theme();
        $informaticsTheme->setName('Informatics');
        $manager->persist($informaticsTheme);

        // Cursus: Web Development
        $webDevCursus = new Cursus();
        $webDevCursus->setTitle('Web Development');
        $webDevCursus->setPrice(60);
        $webDevCursus->setTheme($informaticsTheme);
        $manager->persist($webDevCursus);

        // Leçons for Web Development
        $this->createLesson($manager, $webDevCursus, 'Les langages Html et CSS', 32);
        $this->createLesson($manager, $webDevCursus, 'Dynamiser votre site avec Javascript', 32);

        // Theme: Gardening
        $gardeningTheme = new Theme();
        $gardeningTheme->setName('Gardening');
        $manager->persist($gardeningTheme);

        // Cursus: Gardening 101
        $gardeningCursus = new Cursus();
        $gardeningCursus->setTitle('Gardening 101');
        $gardeningCursus->setPrice(30);
        $gardeningCursus->setTheme($gardeningTheme);
        $manager->persist($gardeningCursus);

        // Leçons for Gardening 101
        $this->createLesson($manager, $gardeningCursus, 'Les outils du jardinier', 16);
        $this->createLesson($manager, $gardeningCursus, 'Jardiner avec la lune', 16);

        // Theme: Cooking
        $cookingTheme = new Theme();
        $cookingTheme->setName('Cooking');
        $manager->persist($cookingTheme);

        // Cursus: Culinary Basics
        $culinaryCursus = new Cursus();
        $culinaryCursus->setTitle('Culinary Basics');
        $culinaryCursus->setPrice(44);
        $culinaryCursus->setTheme($cookingTheme);
        $manager->persist($culinaryCursus);

        // Leçons for Culinary Basics
        $this->createLesson($manager, $culinaryCursus, 'Les modes de cuisson', 23);
        $this->createLesson($manager, $culinaryCursus, 'Les saveurs', 23);

        // Cursus: Food Styling
        $foodStylingCursus = new Cursus();
        $foodStylingCursus->setTitle('Food Styling');
        $foodStylingCursus->setPrice(48);
        $foodStylingCursus->setTheme($cookingTheme);
        $manager->persist($foodStylingCursus);

        // Leçons for Food Styling
        $this->createLesson($manager, $foodStylingCursus, 'Mettre en œuvre le style dans l’assiette', 26);
        $this->createLesson($manager, $foodStylingCursus, 'Harmoniser un repas à quatre plats', 26);

        // Persist all data
        $manager->flush();
    }

    // Helper function to create lessons
    private function createLesson(ObjectManager $manager, Cursus $cursus, string $title, float $price): void
    {
        $lesson = new Leçon();
        $lesson->setTitle($title);
        $lesson->setContent('Lorem ipsum content for ' . $title);
        $lesson->setVideoUrl('https://www.example.com/video/' . strtolower(str_replace(' ', '-', $title)));
        $lesson->setCursus($cursus);
        $manager->persist($lesson);
    }
}
