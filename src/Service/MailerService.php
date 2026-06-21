<?php

namespace App\Service;

use App\Entity\User;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class MailerService
{
    public function __construct(
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly MailerInterface $mailer,
    ) {
    }

    public function sendActivationMail(User $user): void
    {
        $url = $this->urlGenerator->generate('user_activate', [
            'token' => $user->getToken(),
        ], UrlGenerator::ABSOLUTE_URL);

        $text = 'Bonjour, veuillez activer votre compte : '.$url;

        $this->send($user->getEmail(), $text);
    }

    // function to send a password reset email
    public function sendResetPassword(User $user): void
    {
        $url = $this->urlGenerator->generate('reset_password', [
            'token' => $user->getToken(),
        ], UrlGenerator::ABSOLUTE_URL);

        $text = 'Bienvenue sur Désirvoyage!!!,
        Pour réinitialiser votre mot de passe, veuillez cliquer sur le lien ci dessous
        ou copier/coller dans votre navigateur internet.
        '.$url.'
        ---------------
        Ceci est un mail automatique, Merci de ne pas y répondre.';

        $this->send($user->getEmail(), $text);
    }

    private function send(?string $email, string $text): void
    {
        if (null === $email || '' === $email) {
            return;
        }
        $message = (new Email())
            ->from('no-reply@desirvoyage.com')
            ->to($email)
            ->text($text);

        $this->mailer->send($message);
    }

    public function sendContactMessage(string $email): void
    {
        $text = "
                Bonjour,
                votre demande à bien été pris en compte. Elle sera traité dans les plus bref délais.
                Cordialement,
                l'équipe de Désirvoyage";

        $this->send($email, $text);
    }

    public function sendConfirmedPayment(?string $email): void
    {
        $text = '
                Bonjour,
                Votre voyage a bien été réservé, vous pouvez retrouver le détail
                de votre voyage ainsi que votre facture dans votre espace,
                nous vous remercions pour votre achat et nous restons à votre disposition pour tout information.
                DésirVoyage.
                ';

        $this->send($email, $text);
    }
}
