<?php

namespace App\Block\Type;

use App\Block\AbstractBlockType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Hero amuï : logotype géant (le tréma du « ï » en accent) posé sur une grille visible,
 * étiquettes techniques, accroche, texte, deux boutons et un chiffre clé.
 */
class HeroTremaBlock extends AbstractBlockType
{
    public function getKey(): string
    {
        return 'hero_trema';
    }

    public function getLabel(): string
    {
        return 'Hero logotype tréma';
    }

    public function getDescription(): string
    {
        return 'Logotype géant sur grille visible — le « ï » prend le tréma en couleur d\'accent. Étiquettes, accroche, boutons et chiffre clé.';
    }

    public function buildForm(FormBuilderInterface $builder): void
    {
        $builder
            ->add('wordmark', TextType::class, ['label' => 'Logotype', 'required' => false, 'help' => 'Affiché en très grand. Le « ï » reçoit automatiquement le tréma en couleur d\'accent.'])
            ->add('label_left', TextType::class, ['label' => 'Étiquette — gauche', 'required' => false, 'help' => 'Ex : Studio digital'])
            ->add('label_center', TextType::class, ['label' => 'Étiquette — centre', 'required' => false, 'help' => 'Ex : Design · Développement · SEO'])
            ->add('statement', TextType::class, ['label' => 'Accroche', 'required' => false])
            ->add('text', TextareaType::class, ['label' => 'Texte', 'required' => false, 'attr' => ['rows' => 3]])
            ->add('primary_label', TextType::class, ['label' => 'Bouton principal — libellé', 'required' => false])
            ->add('primary_link', TextType::class, ['label' => 'Bouton principal — lien ou ancre', 'required' => false, 'help' => 'Ex : #contact'])
            ->add('secondary_label', TextType::class, ['label' => 'Bouton secondaire — libellé', 'required' => false])
            ->add('secondary_link', TextType::class, ['label' => 'Bouton secondaire — lien ou ancre', 'required' => false])
            ->add('stat_value', TextType::class, ['label' => 'Chiffre clé', 'required' => false, 'help' => 'Ex : 04'])
            ->add('stat_label', TextType::class, ['label' => 'Chiffre clé — légende', 'required' => false, 'help' => 'Ex : projets livrés'])
            ->add('stat_link', TextType::class, ['label' => 'Chiffre clé — lien ou ancre', 'required' => false, 'help' => 'Ex : #projets']);
    }

    public function getDefaultData(): array
    {
        return [
            'wordmark' => 'amuï',
            'label_left' => 'Studio digital',
            'label_center' => 'Design · Développement · SEO',
            'statement' => 'Accroche courte et directe.',
            'text' => '',
            'primary_label' => '',
            'primary_link' => '',
            'secondary_label' => '',
            'secondary_link' => '',
            'stat_value' => '',
            'stat_label' => '',
            'stat_link' => '',
        ];
    }
}
