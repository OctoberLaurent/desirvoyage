<?php

namespace App\Command;

use App\Repository\CategoriesRepository;
use App\Repository\PicturesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class PictureCommand extends Command
{
    protected static $defaultName = 'app:picture';
    private PicturesRepository $picturesRepository;
    private CategoriesRepository $categorieRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(PicturesRepository $picturesRepository, CategoriesRepository $categoriesRepository, EntityManagerInterface $em)
    {
        $this->picturesRepository = $picturesRepository;
        $this->categorieRepository = $categoriesRepository;
        $this->entityManager = $em;
        parent::__construct();
    }

    #[\Override]
    protected function configure()
    {
        $this->setDescription('Rename route of pictures');
    }

    #[\Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        new SymfonyStyle($input, $output);

        $pictures = $this->picturesRepository->findAll();
        $categories = $this->categorieRepository->findAll();

        foreach ($pictures as $picture) {
            $newUrl = $this->pictureName($picture->getUrl());
            $picture->setUrl($newUrl);
            $this->entityManager->persist($picture);
        }

        foreach ($categories as $category) {
            $newUrl = $this->pictureName($category->getUrl());
            $category->setUrl($newUrl);
            $this->entityManager->persist($category);
        }

        $this->entityManager->flush();

        return 0;
    }

    public function pictureName(?string $name): ?string
    {
        $picture = explode('/', $name);
        $secondToLast = max(0, array_key_last($picture) - 1);
        $str = '/Applications/MAMP/htdocs/DésirVoyage/public/'.$picture[$secondToLast].'/'.end($picture);

        return $str;
    }
}
