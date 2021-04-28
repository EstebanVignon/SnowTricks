<?php

declare(strict_types=1);

namespace App\Helpers;

use App\Entity\Picture;
use App\Entity\Trick;
use App\Repository\PictureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class PicturesFilesystemHelper
{
    /**
     * @var Filesystem
     */
    private Filesystem $filesystem;

    /**
     * @var PictureRepository
     */
    private PictureRepository $pictureRepository;

    /**
     * @var ContainerBagInterface
     */
    private ContainerBagInterface $params;

    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $em;

    public function __construct(
        Filesystem $filesystem,
        PictureRepository $pictureRepository,
        ContainerBagInterface $params,
        EntityManagerInterface $em
    ) {
        $this->filesystem = $filesystem;
        $this->pictureRepository = $pictureRepository;
        $this->params = $params;
        $this->em = $em;
    }

    public function deleteTrick(string $pictureId, string $folderParamName)
    {
        $picture = $this->pictureRepository->findOneBy(['id' => $pictureId]);
        if ($picture->getFileName() !== "default.jpg") {
            $this->filesystem->remove(
                $this->params->get($folderParamName) . '/' . $picture->getFileName()
            );
        }
        $this->em->remove($picture);
    }

    public function createPicture(Picture $picture, string $folderParamName, Trick $linkedTrickEntity)
    {
        $file = $picture->getFile();
        $newFilename = md5(uniqid()) . '.' . $file->guessExtension();
        try {
            $file->move(
                $this->params->get($folderParamName),
                $newFilename
            );
        } catch (FileException $e) {
            throw new FileException($e);
        }
        $picture->setFileName($newFilename);
        $picture->setTrick($linkedTrickEntity);
        $this->em->persist($picture);
    }
}
