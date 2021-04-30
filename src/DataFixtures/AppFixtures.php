<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\Picture;
use App\Entity\TokenHistory;
use App\Entity\Trick;
use App\Entity\User;
use App\Entity\Video;
use Cocur\Slugify\Slugify;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private UserPasswordEncoderInterface $encoder;

    private array $tricksData = [
        "Débutant" => [
            "Ollie" => [
                "desc" => "Un Ollie est probablement la première figure de snowboard que vous apprendrez. C'est votre 
                introduction aux sauts de snowboard. Pour exécuter un Ollie, vous devez transférer le poids de votre 
                corps sur votre jambe arrière. Sautez en vous assurant d'être en tête avec votre jambe avant. Levez 
                votre jambe arrière dans l'alignement de votre jambe avant.",
                "video" => "https://www.youtube.com/watch?v=AnI7qGQs0Ic"
            ],
            "Nollie" => [
                "desc" => "Le Nollie est en fait l'opposé d'un Ollie. Quand vous sautez, menez avec votre jambe arrière.
                 Puis, soulevez votre jambe avant pour aligner vos pieds l'un sur l'autre. Vous constaterez probablement
                  que vous pouvez gagner quelques centimètres après quelques essais.",
                "video" => "https://www.youtube.com/watch?v=aAzP3wNT220"
            ],
            "Melon" => [
                "desc" => "Lorsque vous prenez de l'air, tendez le bras vers le bas et attrapez le côté talon de la 
                planche entre vos pieds. Félicitations, vous avez fait votre premier Melon !",
                "video" => "https://www.youtube.com/watch?v=OMxJRz06Ujc"
            ],
            "Indy" => [
                "desc" => "Vous pouvez effectuer un Indy en faisant un Ollie à partir d'un saut et en tendant le bras 
                vers le bas pour attraper la carre de votre planche. Relâchez et repositionnez-vous pour un atterrissage
                 en douceur.",
                "video" => "https://www.youtube.com/watch?v=6yA3XqjTh_w"
            ]
        ],
        "Intermédiaire" => [
            "Tripod" => [
                "desc" => "Le trépied est une figure intermédiaire amusante à apprendre. Pour l'exécuter, vous devez 
                soulever une extrémité de votre planche de la neige et descendre avec les deux mains pour toucher le 
                sol. Si vous le faites correctement, vous créez une connexion en trois points avec le sol, comme un 
                trépied !",
                "video" => "https://www.youtube.com/watch?v=msD1jQL63dA"
            ],
            "Tail Grab" => [
                "desc" => "La prochaine fois que vous prendrez l'air, tendez le bras vers l'arrière pour attraper la 
                queue de votre snowboard.",
                "video" => "https://www.youtube.com/watch?v=gbjwHZDaJLE"
            ],
            "Boardslide" => [
                "desc" => "Le boardslide est comme un 50-50, sauf que vous tournez votre planche perpendiculairement au 
                rail pour pouvoir glisser sur le côté.",
                "video" => "https://www.youtube.com/watch?v=R3OG9rNDIcs"
            ],
            "Back Flip" => [
                "desc" => "Faites attention lorsque vous essayez un backflip. Vous aurez besoin de beaucoup de temps et 
                d'espace pour réaliser le flip avant d'atterrir.",
                "video" => "https://www.youtube.com/watch?v=SlhGVnFPTDE"
            ],
        ],
        "Avancé" => [
            "Front Roll" => [
                "desc" => "Le front roll déplace votre corps vers l'avant, mais il s'incline un peu sur le côté. 
                Maîtrisez-la avant de passer à un flip avant complet.",
                "video" => "https://www.youtube.com/watch?v=xhvqu2XBvI0"
            ],
            "Front Flip" => [
                "desc" => "Le saut périlleux avant est plus difficile que le saut périlleux arrière parce que vous devez
                 résister au mouvement vers le haut que vous obtenez de votre saut. Au lieu de cela, penchez-vous en 
                 avant et rentrez votre corps pour faire une rotation vers l'avant.",
                "video" => "https://www.youtube.com/watch?v=xhvqu2XBvI0"
            ],
            "Frontside 360+" => [
                "desc" => "Tu as probablement déjà deviné. C'est une rotation frontside qui vous fait faire plus d'un 
                tour complet.",
                "video" => "https://www.youtube.com/watch?v=hUddT6FGCws"
            ],
            "Backside 360+" => [
                "desc" => "L'opposé d'un frontside 360+. Maintenir l'équilibre devient difficile quand on recule en 
                tournant.",
                "video" => "https://www.youtube.com/watch?v=hUQ3eKS13co"
            ]
        ]
    ];

    public function __construct(
        UserPasswordEncoderInterface $encoder
    ) {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $factory = Factory::create('fr_FR');

        // CREATE ACTIVE USER
        $activeUser = new User();
        $hash = $this->encoder->encodePassword($activeUser, 'root');
        $activeUser->setUsername('root')
            ->setEmail('root@root.com')
            ->setPassword($hash)
            ->setIsActive(true);
        $manager->persist($activeUser);


        // CREATE NON ACTIVE USER
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

        $pictureCounter = 1;

        foreach ($this->tricksData as $categoryName => $tricks) {
            //CATEGORY
            $cat = new Category();
            $cat->setName($categoryName);
            $slugger = new Slugify();
            $cat->setSlug($slugger->slugify($categoryName));
            $manager->persist($cat);

            //TRICKS
            foreach ($tricks as $trickName => $trickArray) {
                $trick = new Trick();
                $trick->setTitle($trickName)
                    ->setDescription($trickArray["desc"])
                    ->setMainPicture("test-picture-" . $pictureCounter . '.jpg')
                    ->setCategory($cat)
                    ->setUser($activeUser);
                $pictureCounter++;

                // VIDEOS
                $video = new Video();
                $video->setLink($trickArray["video"]);
                $video->setTrick($trick);
                $manager->persist($video);

                // PICTURES
                for ($p = 1; $p <= 3; $p++) {
                    $picture = new Picture();
                    $picture->setFileName($picture::DEFAULT_IMAGE);
                    $picture->setTrick($trick);
                    $manager->persist($picture);
                }

                $manager->persist($trick);
            }
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
