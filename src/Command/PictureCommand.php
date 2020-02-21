<?php

namespace App\Command;

use App\Entity\Categories;
use App\Repository\CategoriesRepository;
use App\Repository\PicturesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PictureCommand extends Command
{
    protected static $defaultName = 'app:picture';
    private $em;

    public function __construct(PicturesRepository $picturesRepository, CategoriesRepository $categoriesRepository,  EntityManagerInterface $em)
    {
        $this->picturesRepository = $picturesRepository;
        $this->categorieRepository = $categoriesRepository;
        $this->entityManager = $em;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setDescription('Rename route of pictures');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        
        $io = new SymfonyStyle($input, $output);

        $pictures = $this->picturesRepository->findall();
        $categories = $this->categorieRepository->findall();

  
        foreach( $pictures as $picture)
        {
            $newUrl= $this->pictureName($picture->getUrl());
            $picture->setUrl($newUrl);
            $this->entityManager->persist($picture);
            
        }

        foreach( $categories as $category)
        {
            $newUrl= $this->pictureName($category->getUrl());
            $category->setUrl($newUrl);
            $this->entityManager->persist($category);
            
        }

        $this->entityManager->flush();

        return 0;
    }

    public function pictureName($name): ?string
    {
        $picture = explode( "/" , $name );
        $secondToLast = (array_key_last($picture)-1);
        $str = '/Applications/MAMP/htdocs/DésirVoyage/public/'.$picture[$secondToLast].'/'.end($picture);
        return $str;
    }
}
