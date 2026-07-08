<?php

namespace App\Tests\Functional;

use App\Entity\ContactMessage;

class FrontSmokeTest extends DatabaseWebTestCase
{
    public function testHomepageDisplaysHero(): void
    {
        $this->client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'amuï studio');
    }

    public function testHomepageContainsSeoTags(): void
    {
        $this->client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('meta[name="description"]');
        $this->assertSelectorExists('link[rel="canonical"]');
        $this->assertSelectorExists('script[type="application/ld+json"]');
    }

    public function testLegalPageIsAccessibleAndNoindex(): void
    {
        $this->client->request('GET', '/mentions-legales');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('meta[name="robots"][content="noindex, nofollow"]');
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
            'website' => 'https://spam.tld', // honeypot rempli
        ]);

        $this->assertResponseRedirects();
        $this->client->followRedirect();
        // Pas de message de succès : la soumission a été ignorée silencieusement
        $this->assertSelectorNotExists('[role="status"]');
        // ... et rien n'est stocké en base
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
