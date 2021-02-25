<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Trick;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\String\Slugger\SluggerInterface;

class AppFixtures extends Fixture
{
    /**
     * @var SluggerInterface
     */
    private $slugger;

    public function __construct(
        SluggerInterface $slugger
    )
    {
        $this->slugger = $slugger;
    }

    public function load(ObjectManager $manager)
    {
        $factory = Factory::create('fr_FR');

        for ($c = 1; $c <= 3; $c++) {

            $cat = new Category();
            $cat->create("Catégorie $c");
            $manager->persist($cat);

            for ($i = 1; $i <= mt_rand(10, 25); $i++) {

                $trick = new Trick();
                $title = "Trick " . $factory->sentence(6);
                $slug = strtolower($this->slugger->slug($title));

                $trick->create(
                    $title,
                    $slug,
                    $factory->sentence(80),
                    null,
                    'https://picsum.photos/id/' . mt_rand(1, 100) . '/300/300'
                );

                $trick->setCategory($cat);

                $manager->persist($trick);
            }
        }

        $manager->flush();
    }
}
