<?php

namespace App\Tests\Configuration;

use PHPUnit\Framework\TestCase;

final class CommentLanguageTest extends TestCase
{
    /** @var list<string> */
    private const DIRECTORIES = ['src', 'tests', 'assets', 'config', 'docker-symfony', 'templates'];

    /** @var list<string> */
    private const EXTENSIONS = ['php', 'js', 'scss', 'yaml', 'yml', 'twig'];

    public function testCodeCommentsAreWrittenInEnglish(): void
    {
        $projectDir = dirname(__DIR__, 2);

        foreach (self::DIRECTORIES as $directory) {
            $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($projectDir.'/'.$directory));
            foreach ($iterator as $file) {
                if (!$file instanceof \SplFileInfo || !$file->isFile() || !in_array($file->getExtension(), self::EXTENSIONS, true)) {
                    continue;
                }
                if ('config' === $directory && 'reference.php' === $file->getFilename()) {
                    continue;
                }

                foreach ($this->commentsIn($file) as $comment) {
                    self::assertDoesNotMatchRegularExpression(
                        '/(?:réserv|voyageur|séjour|entité|contrôleur|données|déjà|génér|démarr|vérifi|charg(?:é|ée|ées|ement)|sûr|mise à jour|à partir|ne doit|uniquement|aucune|quelques|chaîne|champs|erreur|échec|réponse|affichage|soumission|formulaire|prérempl|réinitiali|récup|rendu|désérial|ajout|supprim|sécuris|conserve|retourn|entièrement|prix|tva|fichier|adaptateur|redirig|authentification|accepte|renvoie|persistance|permet|mocker|\b(?:avec|vers|sur|les|des|du|dans|pour|sans|est|sont|mais|ce|ces|cette|notre|votre|leur|leurs|où|car|donc|ni)\b)/ui',
                        $comment,
                        $file->getPathname(),
                    );
                }
            }
        }
    }

    /**
     * @return list<string>
     */
    private function commentsIn(\SplFileInfo $file): array
    {
        $source = (string) file_get_contents($file->getPathname());
        if ('php' === $file->getExtension()) {
            return array_values(array_map(
                static fn (array $token): string => $token[1],
                array_filter(
                    token_get_all($source),
                    static fn (mixed $token): bool => is_array($token) && in_array($token[0], [T_COMMENT, T_DOC_COMMENT], true),
                ),
            ));
        }

        $pattern = match ($file->getExtension()) {
            'twig' => '/\{#[\s\S]*?#\}/m',
            'yaml', 'yml' => '/^\s*#[^\r\n]*/m',
            default => '/\/\/[^\r\n]*|\/\*[\s\S]*?\*\//m',
        };
        preg_match_all($pattern, $source, $matches);

        return $matches[0];
    }
}
