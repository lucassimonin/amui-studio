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
            'tagline' => "Créateurs d'expériences digitales",
            'contact_email' => 'hello@amui.studio',
            'mailer_from' => 'no-reply@amui.studio',
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
            ->setMetaTitle('amuï studio — Portfolio')
            ->setMetaDescription("amuï studio — Créateurs d'expériences digitales. Portfolio one page minimaliste.")
            ->setStructuredData(json_encode([
                '@context' => 'https://schema.org',
                '@type' => 'Organization',
                'name' => 'amuï studio',
                'description' => "Studio digital : direction artistique, design système, front-end créatif.",
                'email' => 'hello@amui.studio',
                'telephone' => '+33600000000',
                'url' => 'https://amui.studio',
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));

        $blocks = [
            ['hero_split', [
                'title' => 'amuï studio :',
                'title_line2' => "Créateurs d'expériences digitales.",
                'text' => "Design minimal, systèmes clairs, interfaces qui vont droit au but. Le contraste est une signature — l'efficacité, une obsession.",
                'primary_label' => 'Démarrer un projet',
                'primary_link' => '#contact',
                'secondary_label' => 'Voir les projets',
                'secondary_link' => '#projets',
                'image' => '',
                'image_alt' => 'Logotype amuï studio',
            ]],
            ['project_grid', [
                'anchor' => 'projets',
                'title' => 'Projets',
                'projects' => [
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
                'title' => "L'Agence",
                'subtitle' => 'Studio digital. Direction artistique, design système, front-end créatif.',
                'statement' => "Le minimalisme au service de l'efficacité.",
                'text' => "Nous construisons des expériences digitales structurées, rapides, et lisibles — du concept à l'interface. Chaque élément doit justifier sa présence. Rien de plus.",
                'boxes' => "Design | Identité · UI System · Typographie\nDéveloppement | Front-end · Performance · Accessibilité",
            ]],
            ['contact_simple', [
                'anchor' => 'contact',
                'title' => 'Contact',
                'text' => "Un message clair, une réponse rapide. Dites-nous ce que vous voulez faire — on vous dira comment le faire bien.",
                'infos_label' => 'Infos',
                'email' => 'hello@amui.studio',
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
            ->setMetaTitle('amuï studio — Portfolio')
            ->setMetaDescription('amuï studio — crafting digital experiences. A minimalist one-page portfolio.')
            ->setStructuredData(json_encode([
                '@context' => 'https://schema.org',
                '@type' => 'Organization',
                'name' => 'amuï studio',
                'description' => 'Digital studio: art direction, design systems, creative front-end.',
                'email' => 'hello@amui.studio',
                'telephone' => '+33600000000',
                'url' => 'https://amui.studio',
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));

        $blocks = [
            ['hero_split', [
                'title' => 'amuï studio:',
                'title_line2' => 'Crafting digital experiences.',
                'text' => 'Minimal design, clear systems, interfaces that get straight to the point. Contrast is a signature — efficiency, an obsession.',
                'primary_label' => 'Start a project',
                'primary_link' => '#contact',
                'secondary_label' => 'See projects',
                'secondary_link' => '#projets',
                'image' => '',
                'image_alt' => 'amuï studio logotype',
            ]],
            ['project_grid', [
                'anchor' => 'projets',
                'title' => 'Projects',
                'projects' => [
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
                'title' => 'The Studio',
                'subtitle' => 'Digital studio. Art direction, design systems, creative front-end.',
                'statement' => 'Minimalism in the service of efficiency.',
                'text' => 'We build structured, fast and legible digital experiences — from concept to interface. Every element must justify its presence. Nothing more.',
                'boxes' => "Design | Identity · UI System · Typography\nDevelopment | Front-end · Performance · Accessibility",
            ]],
            ['contact_simple', [
                'anchor' => 'contact',
                'title' => 'Contact',
                'text' => "A clear message, a quick reply. Tell us what you want to do — we'll tell you how to do it well.",
                'infos_label' => 'Info',
                'email' => 'hello@amui.studio',
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
            'content' => "<h2>Éditeur du site</h2>\n<p>amuï studio — hello@amui.studio.</p>\n<h2>Hébergement</h2>\n<p>À compléter.</p>\n<h2>Données personnelles</h2>\n<p>Les informations transmises via le formulaire de contact sont utilisées uniquement pour répondre à votre demande.</p>",
        ]);
        $page->addBlock($block);

        $manager->persist($page);
    }
}
