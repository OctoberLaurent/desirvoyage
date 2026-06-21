<?php

namespace App\Command;

use App\Repository\CategoriesRepository;
use App\Repository\PicturesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Path;

#[AsCommand(name: 'app:picture', description: 'Rename route of pictures')]
final class PictureCommand extends Command
{
    public function __construct(
        private readonly PicturesRepository $picturesRepository,
        private readonly CategoriesRepository $categoriesRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly string $projectDir,
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
        $parts = explode('/', $name);
        $secondToLast = max(0, array_key_last($parts) - 1);

        return Path::join($this->projectDir, 'public', $parts[$secondToLast] ?? '', $parts[array_key_last($parts)]);
    }
}
