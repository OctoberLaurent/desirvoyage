<?php

namespace App\Command;

use App\Repository\CategoryRepository;
use App\Repository\PictureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'app:picture', description: 'Normalize stored image paths')]
final class PictureCommand extends Command
{
    public function __construct(
        private readonly PictureRepository $picturesRepository,
        private readonly CategoryRepository $categoriesRepository,
        private readonly EntityManagerInterface $entityManager,
    ) {
        parent::__construct();
    }

    #[\Override]
    protected function configure(): void
    {
    }

    #[\Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        new SymfonyStyle($input, $output);

        $pictures = $this->picturesRepository->findAll();
        $categories = $this->categoriesRepository->findAll();

        foreach ($pictures as $picture) {
            $url = $this->pictureName($picture->getUrl());
            if (null !== $url) {
                $picture->setUrl($url);
            }
            $this->entityManager->persist($picture);
        }

        foreach ($categories as $category) {
            $category->setUrl($this->pictureName($category->getUrl()));
            $this->entityManager->persist($category);
        }

        $this->entityManager->flush();

        return Command::SUCCESS;
    }

    public function pictureName(?string $name): ?string
    {
        if (null === $name) {
            return null;
        }

        return '/data/'.basename($name);
    }
}
