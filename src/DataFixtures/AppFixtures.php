<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Trick;
use App\Entity\User;
use App\Entity\Video;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class AppFixtures extends Fixture
{
    /**
     * @var SluggerInterface
     */
    private SluggerInterface $slugger;

    /**
     * @var UserPasswordEncoderInterface
     */
    private UserPasswordEncoderInterface $encoder;

    public function __construct(
        SluggerInterface $slugger,
        UserPasswordEncoderInterface $encoder
    )
    {
        $this->slugger = $slugger;
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $factory = Factory::create('fr_FR');

        $admin = new User();
        $hash = $this->encoder->encodePassword($admin, 'Password');

        $admin->setUsername('Esteban')
            ->setEmail('vignon.esteban@gmail.com')
            ->setPassword($hash)
            ->setRoles(['ROLE_ADMIN']);

        $manager->persist($admin);

        for ($u = 0; $u < 5; $u++) {
            $user = new User();
            $hash = $this->encoder->encodePassword($user, 'Password');
            $user->setUsername("User$u")
                ->setEmail("usertest$u@gmail.com")
                ->setPassword($hash);

            $manager->persist($user);
        }

        for ($c = 1; $c <= 3; $c++) {
            $cat = new Category();
            $cat->create("Catégorie $c");
            $manager->persist($cat);

            for ($i = 1; $i <= mt_rand(10, 25); $i++) {
                $trick = new Trick();
                $title = "Trick " . $factory->sentence(6);
                $trick->setTitle($title)
                    ->setDescription($factory->sentence(20))
                    ->setMainPicture('https://picsum.photos/id/' . mt_rand(1, 100) . '/300/300')
                    ->setCategory($cat);

                for ($v = 1; $v <= 3; $v++) {
                    $video = new Video();
                    $video->setLink('https://www.youtube.com/watch?v=BHACKCNDMW8');
                    $video->setTrick($trick);

                    $manager->persist($video);
                }

                $manager->persist($trick);
            }
        }

        $manager->flush();
    }
}
