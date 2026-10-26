<?php

namespace App\Tests\Functional;

use App\Entity\ContactMessage;

class FrontSmokeTest extends DatabaseWebTestCase
{
    /** Horodatage signé accepté par le formulaire (daté de 10 s pour passer le piège temporel). */
    private function stamp(): string
    {
        $secret = static::getContainer()->getParameter('kernel.secret');
        $time = time() - 10;

        return $time.'.'.substr(hash_hmac('sha256', (string) $time, $secret), 0, 16);
    }

    public function testHomepageDisplaysHero(): void
    {
        $this->client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        // Dès le haut de page : sites ET applications
        $this->assertSelectorTextContains('h1', 'Sites web & applications sur mesure.');
        $this->assertSelectorTextContains('#top', 'Applications');
        $this->assertSelectorTextContains('title', 'Sites web & applications');
        $this->assertSelectorTextContains('#top', 'projets livrés');
    }

    public function testProjectGridNumbersProjectsAndSkipsEmptyLinks(): void
    {
        $crawler = $this->client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('#projets', '[ 01 ]');
        $this->assertCount(20, $crawler->filter('#projets h3'));
        $this->assertSelectorExists('#projets a[href="https://dj-noma.netlify.app"][target="_blank"]');
        // Les projets sans vrai lien (« # ») ne sont pas cliquables
        $this->assertCount(0, $crawler->filter('#projets a[href="#"]'));
    }

    public function testOffersSectionListsPackagesAndOptions(): void
    {
        $crawler = $this->client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('#formules', '[ 02 ]');
        $this->assertSelectorTextContains('#formules', 'Press kit');
        $this->assertSelectorTextContains('#formules', 'One page');
        // Trois formules, quatre options transversales
        $this->assertCount(3, $crawler->filter('#formules dl[data-offres] > div'));
        $this->assertCount(4, $crawler->filter('#formules dl[data-options] > div'));
        $this->assertSelectorExists('#formules a[href="#contact"]');
        // Le menu mène à la nouvelle section
        $this->assertSelectorExists('header a[href="#formules"]');
    }

    public function testAgencySectionShowsSkillsSheet(): void
    {
        $crawler = $this->client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('#agence', '[ 03 ]');
        // Un domaine par ligne, la ponctuation finale de la phrase forte en accent
        $this->assertCount(4, $crawler->filter('#agence dl > div'));
        $this->assertSelectorTextSame('#agence p > span.text-accent', '.');
    }

    public function testContactShowsBigEmailAndNumberedForm(): void
    {
        $crawler = $this->client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('#contact', '[ 04 ]');
        // L'email reste un vrai lien mailto, le « @ » est mis en accent
        $this->assertSelectorExists('#contact a[href="mailto:hello@amui.fr"] span.text-accent');
        $this->assertCount(4, $crawler->filter('#contact form label'));
    }

    public function testFooterSignsOffWithWordmarkAndNumberedMenu(): void
    {
        $crawler = $this->client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('footer .trema .trema-dots');
        $this->assertSelectorTextSame('footer .sr-only', 'amuï');
        $this->assertCount(4, $crawler->filter('footer a[href^="#"] span.font-mono'));
        $this->assertSelectorExists('footer a[href="/mentions-legales"]');
        $this->assertSelectorTextContains('footer', 'Haut de page');
    }

    public function testTremaSignatureKeepsReadableText(): void
    {
        $this->client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        // Le « ï » est dessiné (ı + deux points en accent)…
        $this->assertSelectorExists('#top .trema .trema-dots');
        $this->assertSelectorExists('header .trema');
        // …mais le texte réel reste lu par les lecteurs d'écran
        $this->assertSelectorTextSame('#top .sr-only', 'amuï');
        $this->assertSelectorTextSame('header .sr-only', 'amuï studio');
    }

    public function testHomepageContainsSeoTags(): void
    {
        $this->client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('meta[name="description"]');
        $this->assertSelectorExists('link[rel="canonical"]');
        $this->assertSelectorExists('script[type="application/ld+json"]');
        // Icône du site et image de partage, même sans réglage renseigné
        $this->assertSelectorExists('link[rel="apple-touch-icon"]');
        $this->assertSelectorExists('link[rel="icon"][sizes="32x32"]');
        $this->assertSelectorExists('meta[property="og:image"]');
    }

    public function testLegalPageIsAccessibleAndNoindex(): void
    {
        $this->client->request('GET', '/mentions-legales');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('meta[name="robots"][content="noindex, nofollow"]');
        // Rubriques obligatoires (LCEN + RGPD)
        foreach (['Éditeur du site', 'Hébergement', 'Données personnelles', 'Cookies'] as $section) {
            $this->assertSelectorTextContains('main', $section);
        }
    }

    public function testSitemapListsPublishedIndexablePages(): void
    {
        $this->client->request('GET', '/sitemap.xml');

        $this->assertResponseIsSuccessful();
        $content = (string) $this->client->getResponse()->getContent();
        $this->assertStringContainsString('<urlset', $content);
        // La page mentions légales est noindex : absente du sitemap
        $this->assertStringNotContainsString('mentions-legales', $content);
    }

    public function testUnknownPageReturns404(): void
    {
        $this->client->request('GET', '/page-inexistante');

        $this->assertResponseStatusCodeSame(404);
        // Page 404 éditable depuis l'admin, au design du site
        $this->assertSelectorTextContains('#top', '404');
        $this->assertSelectorTextContains('h1', "Cette page n'existe pas");
        $this->assertSelectorExists('#top a[href="/"]');
    }

    public function testBlogIndexUsesTheJournalHeader(): void
    {
        $this->client->request('GET', '/actualites');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h2', 'Actualités');
        $this->assertSelectorTextContains('main', '[ Journal ]');
    }

    public function testContactFormRejectsHoneypot(): void
    {
        $crawler = $this->client->request('GET', '/');
        $token = $crawler->filter('input[name="_token"]')->attr('value');

        $this->client->request('POST', '/_contact', [
            '_token' => $token,
            'name' => 'Robot',
            'email' => 'robot@spam.tld',
            'message' => 'Un message de robot spammeur',
            'ts' => $this->stamp(),
            'website' => 'https://spam.tld', // honeypot rempli
        ]);

        $this->assertResponseRedirects();
        $this->client->followRedirect();
        // Pas de message de succès : la soumission a été ignorée silencieusement
        $this->assertSelectorNotExists('[role="status"]');
        // ... et rien n'est stocké en base
        $this->assertNull($this->em->getRepository(ContactMessage::class)->findOneBy(['email' => 'robot@spam.tld']));
    }

    public function testContactRejectsInstantAndFloodedSubmissions(): void
    {
        $crawler = $this->client->request('GET', '/');
        $token = $crawler->filter('input[name="_token"]')->attr('value');
        $base = [
            '_token' => $token,
            'name' => 'Robot',
            'email' => 'robot@spam.tld',
            'message' => 'Un message tout à fait ordinaire.',
            'website' => '',
        ];

        // Envoi instantané : horodatage absent ou non signé → ignoré
        $this->client->request('POST', '/_contact', $base + ['ts' => time().'.faux']);
        $this->assertNull($this->em->getRepository(ContactMessage::class)->findOneBy(['email' => 'robot@spam.tld']));

        // Message criblé de liens → ignoré
        $this->client->request('POST', '/_contact', array_merge($base, [
            'ts' => $this->stamp(),
            'message' => 'https://a.tld https://b.tld https://c.tld',
        ]));
        $this->assertNull($this->em->getRepository(ContactMessage::class)->findOneBy(['email' => 'robot@spam.tld']));
    }

    public function testValidContactSubmissionIsStoredForTheBackOffice(): void
    {
        $crawler = $this->client->request('GET', '/');
        $token = $crawler->filter('input[name="_token"]')->attr('value');

        $this->client->request('POST', '/_contact', [
            '_token' => $token,
            'name' => 'Jeanne Client',
            'email' => 'jeanne@exemple.fr',
            'subject' => 'Demande d\'information',
            'message' => 'Bonjour, je souhaite obtenir plus d\'informations.',
            'ts' => $this->stamp(),
            'website' => '',
        ]);

        $this->assertResponseRedirects();

        // L'email part ET une copie est conservée pour le back-office
        $stored = $this->em->getRepository(ContactMessage::class)->findOneBy(['email' => 'jeanne@exemple.fr']);
        $this->assertNotNull($stored, 'Le message doit être enregistré pour le back-office');
        $this->assertSame('Jeanne Client', $stored->getName());
        $this->assertFalse($stored->isRead());
    }
}
