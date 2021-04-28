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
        $hash = $this->encoder->encodePassword($activeUser, 'root');

        $activeUser->setUsername('root')
            ->setEmail('root@root.com')
            ->setPassword($hash)
            ->setIsActive(true);

        $manager->persist($activeUser);

        for ($u = 0; $u < 10; $u++) {
            $user = new User();
            $hash = $this->encoder->encodePassword($user, 'root');
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

        // TRICKS

        for ($i = 1; $i <= 18; $i++) {
            $trick = new Trick();
            $title = "Trick $i " . $factory->sentence(6);
            $trick->setTitle($title)
                ->setDescription($factory->sentence(20))
                ->setMainPicture($trick::DEFAULT_IMAGE)
                ->setCategory($categories[random_int(0, 2)]);

            for ($v = 1; $v <= 3; $v++) {
                $video = new Video();
                $video->setLink('https://www.youtube.com/watch?v=BHACKCNDMW8');
                $video->setTrick($trick);

                $manager->persist($video);
            }

            for ($p = 1; $p <= 3; $p++) {
                $picture = new Picture();
                $picture->setFileName($picture::DEFAULT_IMAGE);
                $picture->setTrick($trick);

                $manager->persist($picture);
            }

            $manager->persist($trick);
        }

        //COMMENTS

        $users = [];
        $tricks = [];
        foreach ($manager->getUnitOfWork()->getScheduledEntityInsertions() as $entity) {
            if ($entity instanceof User) {
                $users[] = $entity;
            }
            if ($entity instanceof Trick) {
                $tricks[] = $entity;
            }
        }
        for ($c = 1; $c <= 200; $c++) {
            $trick = array_rand($tricks);
            $user = array_rand($users);
            $comment = new Comment();
            $comment->setContent($factory->realText(rand(10, 140)));
            $comment->setTrick($tricks[$trick]);
            $comment->setUser($users[$user]);
            $manager->persist($comment);
        }

        $manager->flush();
    }
}
