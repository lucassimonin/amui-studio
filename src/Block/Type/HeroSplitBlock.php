<?php

namespace App\Block\Type;

use App\Block\AbstractBlockType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class HeroSplitBlock extends AbstractBlockType
{
    public function getKey(): string
    {
        return 'hero_split';
    }

    public function getLabel(): string
    {
        return 'Hero deux colonnes';
    }

    public function getDescription(): string
    {
        return 'Titre sur deux lignes, texte et deux boutons à gauche — visuel/logotype à droite.';
    }

    public function buildForm(FormBuilderInterface $builder): void
    {
        $builder
            ->add('title', TextType::class, ['label' => 'Titre — ligne 1', 'required' => false])
            ->add('title_line2', TextType::class, ['label' => 'Titre — ligne 2', 'required' => false])
            ->add('text', TextareaType::class, ['label' => 'Texte', 'required' => false, 'attr' => ['rows' => 3]])
            ->add('primary_label', TextType::class, ['label' => 'Bouton principal — libellé', 'required' => false])
            ->add('primary_link', TextType::class, ['label' => 'Bouton principal — lien ou ancre', 'required' => false, 'help' => 'Ex: #contact'])
            ->add('secondary_label', TextType::class, ['label' => 'Bouton secondaire — libellé', 'required' => false])
            ->add('secondary_link', TextType::class, ['label' => 'Bouton secondaire — lien ou ancre', 'required' => false])
            ->add('image', TextType::class, ['label' => 'Visuel de droite (URL)', 'required' => false, 'help' => 'Collez une URL depuis la bibliothèque Médias — vide : le titre occupe toute la largeur'])
            ->add('image_alt', TextType::class, ['label' => 'Texte alternatif du visuel', 'required' => false]);
    }

    public function getDefaultData(): array
    {
        return [
            'title' => 'Titre principal :',
            'title_line2' => 'Deuxième ligne.',
            'text' => '',
            'primary_label' => '',
            'primary_link' => '',
            'secondary_label' => '',
            'secondary_link' => '',
            'image' => '',
            'image_alt' => '',
        ];
    }
}
