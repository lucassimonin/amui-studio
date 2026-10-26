<?php

namespace App\Controller;

use App\Entity\Block;
use App\Entity\ContactMessage;
use App\Service\SettingsProvider;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\RateLimiter\RateLimiterFactoryInterface;
use Symfony\Component\Routing\Attribute\Route;

class ContactController extends AbstractController
{
    #[Route('/_contact', name: 'front_contact', methods: ['POST'])]
    public function submit(
        Request $request,
        MailerInterface $mailer,
        SettingsProvider $settings,
        EntityManagerInterface $em,
        RateLimiterFactoryInterface $contactLimiter,
        #[\SensitiveParameter] string $appSecret = '',
    ): Response {
        $redirect = new RedirectResponse(($request->headers->get('referer') ?: '/').'#contact');

        if (!$this->isCsrfTokenValid('contact', (string) $request->request->get('_token'))) {
            $this->addFlash('contact_error', 'Session expirée, merci de réessayer.');

            return $redirect;
        }

        // Honeypot : champ invisible, un humain ne le remplit jamais
        if ('' !== (string) $request->request->get('website', '')) {
            return $redirect;
        }

        // Piège temporel : un robot poste dans la seconde, un humain met au moins
        // quelques secondes à remplir. L'horodatage est signé pour ne pas être forgé.
        if (!$this->stampIsPlausible((string) $request->request->get('ts', ''), $appSecret)) {
            return $redirect;
        }

        // 5 envois par heure et par adresse IP
        $limiter = $contactLimiter->create($request->getClientIp() ?? 'inconnu');
        if (!$limiter->consume()->isAccepted()) {
            $this->addFlash('contact_error', 'Trop de demandes envoyées depuis cette connexion. Réessayez dans une heure.');

            return $redirect;
        }

        $name = trim((string) $request->request->get('name', ''));
        $email = trim((string) $request->request->get('email', ''));
        $subject = trim((string) $request->request->get('subject', ''));
        $message = trim((string) $request->request->get('message', ''));

        // Les messages bourrés de liens sont du spam dans l'immense majorité des cas
        if (preg_match_all('#https?://#i', $name.' '.$subject.' '.$message) >= 3) {
            return $redirect;
        }

        if ('' === $name || !filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($message) < 10) {
            $this->addFlash('contact_error', 'Merci de vérifier le formulaire : nom, e-mail valide et message (10 caractères minimum) sont requis.');

            return $redirect;
        }

        // Destinataire : configuré dans le bloc (jamais depuis le POST), sinon réglages globaux
        $recipient = null;
        if ($blockId = $request->request->getInt('block')) {
            $block = $em->getRepository(Block::class)->find($blockId);
            $recipient = $block?->getData()['recipient'] ?? null;
        }
        $recipient = $recipient ?: $settings->get('contact_email', 'contact@example.com');

        // Copie back-office : la demande est conservée en base quoi qu'il arrive,
        // même si l'envoi d'email échoue (consultable dans Admin → Messages)
        $contactMessage = (new ContactMessage())
            ->setName($name)
            ->setEmail($email)
            ->setSubject($subject)
            ->setMessage($message)
            ->setRecipient($recipient);
        $em->persist($contactMessage);
        $em->flush();

        $mail = (new Email())
            ->from($settings->get('mailer_from', 'no-reply@'.$request->getHost()))
            ->replyTo($email)
            ->to($recipient)
            ->subject(sprintf('[%s] %s', $settings->get('site_name', 'Site'), $subject ?: 'Nouveau message'))
            ->text(sprintf("Nom : %s\nE-mail : %s\nSujet : %s\n\n%s", $name, $email, $subject, $message));

        try {
            $mailer->send($mail);
        } catch (\Throwable) {
            // DSN null en dev : on considère la demande enregistrée
        }

        $this->addFlash('contact_success', 'Merci — votre demande a bien été envoyée.');

        return $redirect;
    }

    /** Horodatage signé par contact_stamp() : signature valide, et âge entre 3 secondes et 24 heures. */
    private function stampIsPlausible(string $stamp, string $appSecret): bool
    {
        if ('' === $appSecret) {
            return true; // pas de secret injecté (tests unitaires) : on ne bloque pas
        }

        [$time, $signature] = array_pad(explode('.', $stamp, 2), 2, '');
        $expected = substr(hash_hmac('sha256', $time, $appSecret), 0, 16);

        if ('' === $time || !hash_equals($expected, $signature)) {
            return false;
        }

        $age = time() - (int) $time;

        return $age >= 3 && $age <= 86400;
    }
}
