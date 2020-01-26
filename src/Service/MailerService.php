<?php
namespace App\Service;



use Swift_Mailer;
use Swift_Message;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;


class MailerService{
    private $urlGenerator;
    private $mailer;


    public function __construct(UrlGeneratorInterface $urlGenerator, Swift_Mailer $mailer)
    {
        $this->urlGenerator = $urlGenerator;
        $this->mailer = $mailer;
    }
    public function sendActivationMail(User $user)
    {
        $url = $this->urlGenerator->generate( 'user_activate', array(
            'token' => $user->getToken(),
        ), UrlGenerator::ABSOLUTE_URL);

        $text = 'Bonjour, veuillez activer votre compte : ' . $url;

        $this->send( $user->getEmail(), $text );
    }
    // fonction pour envoyer un mail de rehinitialisation de mot de passe
    public function sendResetPassword( User $user)
    {
        $url = $this->urlGenerator->generate('reset_password', array(
            'token' => $user->getToken(),
        ), UrlGenerator::ABSOLUTE_URL);

        $text = "Bienvenue sur Désirvoyage!!!,
        Pour réinitialiser votre mot de passe, veuillez cliquer sur le lien ci dessous
        ou copier/coller dans votre navigateur internet.
        ". $url ."
        ---------------
        Ceci est un mail automatique, Merci de ne pas y répondre.";
        
        $this->send( $user->getEmail(), $text);
    }
    private function send( $email, $text ){
        $message = new Swift_Message();
        $message->setFrom( 'no-reply@desirvoyage.com' );
        $message->setTo( $email );
        $message->setBody( $text );

        $this->mailer->send( $message );
    }
 
}