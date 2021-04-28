<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\Picture;
use App\Entity\TokenHistory;
use App\Entity\Trick;
use App\Entity\User;
use App\Entity\Video;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Ramsey\Uuid\Uuid;
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
    ) {
        $this->slugger = $slugger;
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $factory = Factory::create('fr_FR');

        $activeUser = new User();
        $hash = $this->encoder->encodePassword($activeUser, 'Password');

        $activeUser->setUsername('Esteban')
            ->setEmail('vignon.esteban@gmail.com')
            ->setPassword($hash)
            ->setIsActive(true);

        $manager->persist($activeUser);

        for ($u = 0; $u < 10; $u++) {
            $user = new User();
            $hash = $this->encoder->encodePassword($user, 'Password');
            $user->setUsername("User$u")
                ->setEmail("usertest$u@gmail.com")
                ->setPassword($hash);

            $token = new TokenHistory();
            $token->setType('registration')
                ->setValue(Uuid::uuid4()->toString())
                ->setUser($user)
                ->setCreatedAt(new \DateTime('-' . $u . ' days'));

            $manager->persist($user);
            $manager->persist($token);
        }

        $categories = [];
        for ($c = 1; $c <= 3; $c++) {
            $cat = new Category();
            $cat->create("Catégorie $c");
            $manager->persist($cat);
            $categories[] = $cat;
        }

        for ($i = 1; $i <= 18; $i++) {
            $trick = new Trick();
            $title = "Trick $i " . $factory->sentence(6);
            $trick->setTitle($title)
                ->setDescription($factory->sentence(20))
                ->setMainPicture('default.jpg')
                ->setCategory($categories[random_int(0, 2)]);

            for ($v = 1; $v <= 3; $v++) {
                $video = new Video();
                $video->setLink('https://www.youtube.com/watch?v=BHACKCNDMW8');
                $video->setTrick($trick);

                $manager->persist($video);
            }

            for ($p = 1; $p <= 3; $p++) {
                $picture = new Picture();
                $picture->setFileName('default.jpg');
                $picture->setTrick($trick);

                $manager->persist($picture);
            }

            $users = $manager->getUnitOfWork()->getScheduledEntityInsertions();
            foreach ($users as $user) {
                if (is_a($user, User::class)) {
                    for ($c = 1; $c <= rand(0, 2); $c++) {
                        $comment = new Comment();
                        $comment->setContent(
                            $user->getUsername() . ' ' .
                            $trick->getTitle() . ' ' .
                            $factory->realText(rand(10, 140))
                        );
                        $comment->setTrick($trick);
                        $comment->setUser($user);
                        $manager->persist($comment);
                    }
                }
            }

            $manager->persist($trick);
        }


        $manager->flush();
    }
}
