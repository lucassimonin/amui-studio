<?php

namespace App\Block\Type;

use App\Block\AbstractBlockType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class SplitIntroBlock extends AbstractBlockType
{
    public function getKey(): string
    {
        return 'split_intro';
    }

    public function getLabel(): string
    {
        return 'Deux colonnes (titre + manifeste)';
    }

    public function getDescription(): string
    {
        return 'Titre et intro à gauche — phrase forte, texte et encadrés à droite.';
    }

    public function buildForm(FormBuilderInterface $builder): void
    {
        $builder
            ->add('anchor', TextType::class, ['label' => 'Ancre HTML', 'required' => false, 'help' => 'Ex: agence → lien #agence'])
            ->add('title', TextType::class, ['label' => 'Titre', 'required' => false])
            ->add('subtitle', TextareaType::class, ['label' => 'Intro (sous le titre)', 'required' => false, 'attr' => ['rows' => 2]])
            ->add('statement', TextType::class, ['label' => 'Phrase forte', 'required' => false])
            ->add('text', TextareaType::class, ['label' => 'Texte', 'required' => false, 'attr' => ['rows' => 4]])
            ->add('boxes', TextareaType::class, [
                'label' => 'Encadrés (un par ligne : libellé | contenu)',
                'required' => false,
                'attr' => ['rows' => 3],
                'help' => 'Ex: Design | Identité · UI System · Typographie',
            ]);
    }

    public function getDefaultData(): array
    {
        return [
            'anchor' => '',
            'title' => 'Titre de section',
            'subtitle' => '',
            'statement' => '',
            'text' => '',
            'boxes' => '',
        ];
    }
}
