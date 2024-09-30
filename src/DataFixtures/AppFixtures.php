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
        // Create Themes
        $themes = [
            'Music' => ['Guitar Basics', 'Piano Basics'],
            'Informatics' => ['Web Development'],
            'Gardening' => ['Gardening 101'],
            'Cooking' => ['Culinary Basics', 'Food Styling']
        ];

        foreach ($themes as $themeName => $cursusTitles) {
            $theme = new Theme();
            $theme->setName($themeName);
            $manager->persist($theme);

            // Create Cursus for each Theme
            foreach ($cursusTitles as $cursusTitle) {
                $cursus = new Cursus();
                $cursus->setTitle($cursusTitle);
                $cursus->setPrice(mt_rand(20, 100)); // Random price for demonstration
                $cursus->setTheme($theme);
                $manager->persist($cursus);

                // Create Lessons for each Cursus
                for ($i = 1; $i <= 3; $i++) {
                    $lesson = new Leçon();
                    $lesson->setTitle("$cursusTitle - Lesson $i");
                    $lesson->setContent("Lorem ipsum content for lesson $i of $cursusTitle.");
                    $lesson->setVideoUrl("https://www.example.com/video/$i"); // Example video URL
                    $lesson->setCursus($cursus);
                    $manager->persist($lesson);
                }
            }
        }

        // Flush to write everything to the database
        $manager->flush();
    }
}
