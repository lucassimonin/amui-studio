<?php

namespace App\DataFixtures;

use App\Entity\Block;
use App\Entity\NavigationItem;
use App\Entity\Page;
use App\Entity\Setting;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Site amuï studio (intégration de la maquette portfolio one-page).
 * Connexion admin : admin@agence.fr / admin
 */
class AppFixtures extends Fixture
{
    public function __construct(private readonly UserPasswordHasherInterface $hasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $this->loadUser($manager);
        $this->loadSettings($manager);
        $this->loadNavigation($manager);
        $this->loadHomepage($manager);
        $this->loadLegalPage($manager);

        $manager->flush();
    }

    private function loadUser(ObjectManager $manager): void
    {
        $user = new User();
        $user->setEmail('admin@agence.fr')
            ->setRoles(['ROLE_ADMIN'])
            ->setPassword($this->hasher->hashPassword($user, 'admin'));
        $manager->persist($user);
    }

    private function loadSettings(ObjectManager $manager): void
    {
        $settings = [
            'site_name' => 'amuï studio',
            'tagline' => 'Sites web & applications sur mesure',
            'contact_email' => 'hello@amui.fr',
            'mailer_from' => 'no-reply@amui.fr',
            'phone' => '+33 6 00 00 00 00',
            'footer_text' => 'amuï studio. Minimalisme radical.',
            'linkedin' => 'https://www.linkedin.com/',
        ];

        foreach ($settings as $key => $value) {
            $manager->persist((new Setting())->setKey($key)->setValue($value));
        }
    }

    private function loadNavigation(ObjectManager $manager): void
    {
        $header = [
            ['Projets', '#projets'],
            ['Agence', '#agence'],
            ['Contact', '#contact'],
        ];

        foreach ($header as $position => [$label, $url]) {
            $manager->persist((new NavigationItem())
                ->setLabel($label)->setUrl($url)
                ->setLocation(NavigationItem::LOCATION_HEADER)
                ->setPosition($position));
        }

        // Menu de la version anglaise
        $headerEn = [
            ['Projects', '#projets'],
            ['Studio', '#agence'],
            ['Contact', '#contact'],
        ];

        foreach ($headerEn as $position => [$label, $url]) {
            $manager->persist((new NavigationItem())
                ->setLabel($label)->setUrl($url)->setLocale('en')
                ->setLocation(NavigationItem::LOCATION_HEADER)
                ->setPosition($position));
        }

        $manager->persist((new NavigationItem())
            ->setLabel('Mentions légales')->setUrl('/mentions-legales')
            ->setLocation(NavigationItem::LOCATION_FOOTER)->setPosition(0));
    }

    private function loadHomepage(ObjectManager $manager): void
    {
        $page = new Page();
        $page->setTitle('amuï studio — Portfolio')
            ->setSlug('accueil')
            ->setIsHomepage(true)
            ->setStatus(Page::STATUS_PUBLISHED)
            ->setMetaTitle('amuï studio — Sites web & applications')
            ->setMetaDescription("amuï studio — Sites web et applications sur mesure : design minimal, développement web et mobile, SEO.")
            ->setStructuredData(json_encode([
                '@context' => 'https://schema.org',
                '@type' => 'Organization',
                'name' => 'amuï studio',
                'description' => "Studio digital : sites web et applications sur mesure, direction artistique, design système, développement web et mobile.",
                'email' => 'hello@amui.fr',
                'telephone' => '+33600000000',
                'url' => 'https://amui.fr',
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));

        $blocks = [
            ['hero_trema', [
                'wordmark' => 'amuï',
                'label_left' => 'Studio digital',
                'label_center' => 'Sites · Applications · SEO',
                'statement' => 'Sites web & applications sur mesure.',
                'text' => "Du site vitrine à l'application web ou mobile : design minimal, systèmes clairs, interfaces qui vont droit au but.",
                'primary_label' => 'Démarrer un projet',
                'primary_link' => '#contact',
                'secondary_label' => 'Voir les projets',
                'secondary_link' => '#projets',
                'stat_value' => '10',
                'stat_label' => 'projets livrés',
                'stat_link' => '#projets',
            ]],
            ['project_grid', [
                'anchor' => 'projets',
                'index' => '01',
                'title' => 'Projets',
                'projects' => [
                    [
                        'image' => '/images/projets/cote-carnon.webp',
                        'image_alt' => 'Aperçu du site Côté Carnon, conciergerie de locations saisonnières',
                        'title' => 'Côté Carnon',
                        'meta' => 'Conciergerie · Vitrine · Locations saisonnières',
                        'year' => '2026',
                        'url' => '',
                    ],
                    [
                        'image' => '/images/projets/oplaa.webp',
                        'image_alt' => "Aperçu d'Oplaa, application de planning d'équipe",
                        'title' => 'Oplaa',
                        'meta' => 'Application web · Planning · Supabase',
                        'year' => '2026',
                        'url' => 'https://oplaa.pro',
                    ],
                    [
                        'image' => '/images/projets/jats-carnet.webp',
                        'image_alt' => 'Aperçu du site de JATS, producteur de musique électronique',
                        'title' => 'JATS — Le Carnet',
                        'meta' => 'Site artiste · Presse · Booking',
                        'year' => '2026',
                        'url' => 'https://jats-carnet.netlify.app',
                    ],
                    [
                        'image' => '/images/projets/jats-power.webp',
                        'image_alt' => 'Aperçu de la carte de visite numérique de JATS',
                        'title' => 'JATS — La Carte',
                        'meta' => 'One page · Carte numérique · Presse',
                        'year' => '2026',
                        'url' => 'https://jats-power.netlify.app',
                    ],
                    [
                        'image' => '/images/projets/noma.webp',
                        'image_alt' => 'Aperçu du site de Noma, DJ et producteur',
                        'title' => 'Noma',
                        'meta' => 'DJ · Portfolio artiste · Booking',
                        'year' => '2026',
                        'url' => 'https://dj-noma.netlify.app',
                    ],
                    [
                        'image' => '/images/projets/ludorules.webp',
                        'image_alt' => 'Aperçu du site Ludorules, catalogue de jeux de cartes et de dés',
                        'title' => 'Ludorules',
                        'meta' => 'Application web · Catalogue de jeux · Trilingue',
                        'year' => '',
                        'url' => 'https://ludorules.com',
                    ],
                    [
                        'image' => 'https://images.unsplash.com/photo-1621905251189-08b45d6a269e?auto=format&fit=crop&w=1200&q=80',
                        'image_alt' => "Aperçu du projet L'Atelier Plomberie",
                        'title' => "L'Atelier Plomberie",
                        'meta' => 'Identité · Site · SEO',
                        'url' => '#',
                    ],
                    [
                        'image' => 'https://nuans-salon.com/wp-content/uploads/2018/12/Nuans-marchedulez-ambiance1-min.png',
                        'image_alt' => 'Aperçu du projet Nuans — salon de coiffure',
                        'title' => 'Nuans',
                        'meta' => 'E-commerce · UI System',
                        'url' => '#',
                    ],
                    [
                        'image' => 'https://www.referencebois.fr/wp-content/uploads/2024/12/terrasse-bois.jpg',
                        'image_alt' => 'Aperçu du projet Référence Bois',
                        'title' => 'Référence Bois',
                        'meta' => 'Terrasse · Bardage · Vitrine',
                        'url' => '#',
                    ],
                    [
                        'image' => 'https://www.bivouakcafe.fr/wp-content/uploads/2022/04/wim-lippens-BC02_HD-0031-1920x1091.jpg',
                        'image_alt' => 'Aperçu du projet Bivouak Café',
                        'title' => 'Bivouak Café',
                        'meta' => 'Coffee shop · Lifestyle · One page',
                        'url' => '#',
                    ],
                ],
            ]],
            ['split_intro', [
                'anchor' => 'agence',
                'index' => '02',
                'title' => "L'Agence",
                'subtitle' => 'Studio digital. Direction artistique, design système, front-end créatif.',
                'statement' => "Le minimalisme au service de l'efficacité.",
                'text' => "Nous construisons des expériences digitales structurées, rapides, et lisibles — du concept à l'interface. Chaque élément doit justifier sa présence. Rien de plus.",
                'boxes' => "Design | Identité · UI System · Typographie\nDéveloppement | Sites · Applications web & mobile · Performance\nSEO | Référencement · Données structurées · Visibilité locale\nMaintenance | Mises à jour · Sécurité · Sauvegardes",
            ]],
            ['contact_simple', [
                'anchor' => 'contact',
                'index' => '03',
                'title' => 'Contact',
                'text' => "Un message clair, une réponse rapide. Dites-nous ce que vous voulez faire — on vous dira comment le faire bien.",
                'infos_label' => 'Écrivez-nous',
                'email' => 'hello@amui.fr',
                'phone' => '+33 6 00 00 00 00',
                'form_note' => 'En envoyant, vous acceptez un retour par email.',
                'recipient' => '',
            ]],
        ];

        foreach ($blocks as $position => [$type, $data]) {
            $block = new Block();
            $block->setType($type)->setData($data)->setPosition($position);
            $page->addBlock($block);
        }

        $manager->persist($page);
        $this->loadEnglishHomepage($manager, $page);
    }

    /** Version anglaise de la homepage — même translationGroup (hreflang + sélecteur de langue) */
    private function loadEnglishHomepage(ObjectManager $manager, Page $frenchHomepage): void
    {
        $page = new Page();
        $page->setTitle('amuï studio — Portfolio')
            ->setSlug('home')
            ->setLocale('en')
            ->setTranslationGroup($frenchHomepage->getTranslationGroup())
            ->setIsHomepage(true)
            ->setStatus(Page::STATUS_PUBLISHED)
            ->setMetaTitle('amuï studio — Websites & apps')
            ->setMetaDescription('amuï studio — custom websites and apps: minimal design, web and mobile development, SEO.')
            ->setStructuredData(json_encode([
                '@context' => 'https://schema.org',
                '@type' => 'Organization',
                'name' => 'amuï studio',
                'description' => 'Digital studio: custom websites and apps, art direction, design systems, web and mobile development.',
                'email' => 'hello@amui.fr',
                'telephone' => '+33600000000',
                'url' => 'https://amui.fr',
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));

        $blocks = [
            ['hero_trema', [
                'wordmark' => 'amuï',
                'label_left' => 'Digital studio',
                'label_center' => 'Websites · Apps · SEO',
                'statement' => 'Custom websites & apps.',
                'text' => 'From showcase websites to web and mobile apps: minimal design, clear systems, interfaces that get straight to the point.',
                'primary_label' => 'Start a project',
                'primary_link' => '#contact',
                'secondary_label' => 'See projects',
                'secondary_link' => '#projets',
                'stat_value' => '10',
                'stat_label' => 'projects shipped',
                'stat_link' => '#projets',
            ]],
            ['project_grid', [
                'anchor' => 'projets',
                'index' => '01',
                'title' => 'Projects',
                'projects' => [
                    [
                        'image' => '/images/projets/cote-carnon.webp',
                        'image_alt' => 'Preview of Côté Carnon, a holiday rental concierge service',
                        'title' => 'Côté Carnon',
                        'meta' => 'Concierge service · Showcase · Holiday rentals',
                        'year' => '2026',
                        'url' => '',
                    ],
                    [
                        'image' => '/images/projets/oplaa.webp',
                        'image_alt' => 'Preview of Oplaa, a team scheduling web app',
                        'title' => 'Oplaa',
                        'meta' => 'Web app · Scheduling · Supabase',
                        'year' => '2026',
                        'url' => 'https://oplaa.pro',
                    ],
                    [
                        'image' => '/images/projets/jats-carnet.webp',
                        'image_alt' => 'Preview of the website of JATS, electronic music producer',
                        'title' => 'JATS — Press kit',
                        'meta' => 'Artist site · Press · Booking',
                        'year' => '2026',
                        'url' => 'https://jats-carnet.netlify.app',
                    ],
                    [
                        'image' => '/images/projets/jats-power.webp',
                        'image_alt' => 'Preview of the digital business card of JATS',
                        'title' => 'JATS — Card',
                        'meta' => 'One page · Digital card · Press',
                        'year' => '2026',
                        'url' => 'https://jats-power.netlify.app',
                    ],
                    [
                        'image' => '/images/projets/noma.webp',
                        'image_alt' => 'Preview of the website of Noma, DJ and producer',
                        'title' => 'Noma',
                        'meta' => 'DJ · Artist portfolio · Booking',
                        'year' => '2026',
                        'url' => 'https://dj-noma.netlify.app',
                    ],
                    [
                        'image' => '/images/projets/ludorules.webp',
                        'image_alt' => 'Preview of Ludorules, a catalogue of card and dice games',
                        'title' => 'Ludorules',
                        'meta' => 'Web app · Game catalogue · Trilingual',
                        'year' => '',
                        'url' => 'https://ludorules.com',
                    ],
                    [
                        'image' => 'https://images.unsplash.com/photo-1621905251189-08b45d6a269e?auto=format&fit=crop&w=1200&q=80',
                        'image_alt' => "Preview of the L'Atelier Plomberie project",
                        'title' => "L'Atelier Plomberie",
                        'meta' => 'Identity · Website · SEO',
                        'url' => '#',
                    ],
                    [
                        'image' => 'https://nuans-salon.com/wp-content/uploads/2018/12/Nuans-marchedulez-ambiance1-min.png',
                        'image_alt' => 'Preview of the Nuans hair salon project',
                        'title' => 'Nuans',
                        'meta' => 'E-commerce · UI System',
                        'url' => '#',
                    ],
                    [
                        'image' => 'https://www.referencebois.fr/wp-content/uploads/2024/12/terrasse-bois.jpg',
                        'image_alt' => 'Preview of the Référence Bois project',
                        'title' => 'Référence Bois',
                        'meta' => 'Decking · Cladding · Showcase',
                        'url' => '#',
                    ],
                    [
                        'image' => 'https://www.bivouakcafe.fr/wp-content/uploads/2022/04/wim-lippens-BC02_HD-0031-1920x1091.jpg',
                        'image_alt' => 'Preview of the Bivouak Café project',
                        'title' => 'Bivouak Café',
                        'meta' => 'Coffee shop · Lifestyle · One page',
                        'url' => '#',
                    ],
                ],
            ]],
            ['split_intro', [
                'anchor' => 'agence',
                'index' => '02',
                'title' => 'The Studio',
                'subtitle' => 'Digital studio. Art direction, design systems, creative front-end.',
                'statement' => 'Minimalism in the service of efficiency.',
                'text' => 'We build structured, fast and legible digital experiences — from concept to interface. Every element must justify its presence. Nothing more.',
                'boxes' => "Design | Identity · UI System · Typography\nDevelopment | Websites · Web & mobile apps · Performance\nSEO | Search · Structured data · Local visibility\nMaintenance | Updates · Security · Backups",
            ]],
            ['contact_simple', [
                'anchor' => 'contact',
                'index' => '03',
                'title' => 'Contact',
                'text' => "A clear message, a quick reply. Tell us what you want to do — we'll tell you how to do it well.",
                'infos_label' => 'Write to us',
                'email' => 'hello@amui.fr',
                'phone' => '+33 6 00 00 00 00',
                'form_note' => 'By sending this, you agree to a reply by email.',
                'recipient' => '',
            ]],
        ];

        foreach ($blocks as $position => [$type, $data]) {
            $block = new Block();
            $block->setType($type)->setData($data)->setPosition($position);
            $page->addBlock($block);
        }

        $manager->persist($page);
    }

    private function loadLegalPage(ObjectManager $manager): void
    {
        $page = new Page();
        $page->setTitle('Mentions légales')
            ->setSlug('mentions-legales')
            ->setStatus(Page::STATUS_PUBLISHED)
            ->setNoindex(true)
            ->setMetaTitle('Mentions légales — amuï studio');

        $block = new Block();
        $block->setType('rich_text')->setPosition(0)->setData([
            'kicker' => '',
            'title' => 'Mentions légales',
            'content' => "<h2>Éditeur du site</h2>\n<p>Le site amui.fr est édité par amuï studio, <mark>[à compléter : statut juridique, ex. entreprise individuelle / SAS au capital de …]</mark>.</p>\n<ul>\n<li>Responsable : <mark>[à compléter : nom et prénom]</mark></li>\n<li>Adresse : <mark>[à compléter : adresse postale du siège]</mark></li>\n<li>SIRET : <mark>[à compléter : numéro SIRET]</mark></li>\n<li>TVA intracommunautaire : <mark>[à compléter : numéro, ou « TVA non applicable, art. 293 B du CGI »]</mark></li>\n<li>Email : <a href=\"mailto:hello@amui.fr\">hello@amui.fr</a></li>\n<li>Téléphone : <mark>[à compléter : numéro]</mark></li>\n</ul>\n<h2>Directeur de la publication</h2>\n<p><mark>[à compléter : nom et prénom du directeur de la publication]</mark></p>\n<h2>Hébergement</h2>\n<p><mark>[à compléter : nom de l’hébergeur, adresse et téléphone]</mark></p>\n<h2>Propriété intellectuelle</h2>\n<p>L’ensemble des contenus de ce site (textes, logotype, éléments graphiques, mise en page) est la propriété d’amuï studio, sauf mention contraire. Toute reproduction, même partielle, est interdite sans autorisation écrite préalable.</p>\n<p>Les visuels des projets présentés dans le portfolio restent la propriété de leurs titulaires respectifs et sont reproduits à titre de références.</p>\n<h2>Données personnelles</h2>\n<p>Les informations transmises via le formulaire de contact (nom, email, sujet, message) sont utilisées uniquement pour répondre à votre demande. Elles sont destinées à amuï studio et ne sont ni cédées ni revendues.</p>\n<p>Responsable du traitement : amuï studio. Base légale : votre demande de contact. Durée de conservation : <mark>[à compléter : durée, ex. 3 ans après le dernier échange]</mark>.</p>\n<p>Conformément au Règlement général sur la protection des données (RGPD) et à la loi Informatique et Libertés, vous disposez d’un droit d’accès, de rectification, d’effacement, d’opposition, de limitation et de portabilité de vos données. Pour l’exercer, écrivez à <a href=\"mailto:hello@amui.fr\">hello@amui.fr</a>. Vous pouvez également introduire une réclamation auprès de la CNIL (<a href=\"https://www.cnil.fr\">cnil.fr</a>).</p>\n<h2>Cookies</h2>\n<p>Ce site ne dépose aucun cookie publicitaire ni de mesure d’audience. Seul un cookie technique de session est utilisé pour sécuriser le formulaire de contact ; il est strictement nécessaire au fonctionnement du site et ne requiert pas de consentement.</p>\n<h2>Crédits</h2>\n<p>Conception, design et développement : amuï studio.</p>",
        ]);
        $page->addBlock($block);

        $manager->persist($page);
    }
}
