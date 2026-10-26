<?php

namespace App\Block\Type;

use App\Block\AbstractBlockType;
use App\Form\OfferItemType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Formules proposées : une ligne par offre (nom, phrase, ce qui est compris),
 * suivie des options transversales (SEO, maintenance…).
 */
class OfferListBlock extends AbstractBlockType
{
    public function getKey(): string
    {
        return 'offer_list';
    }

    public function getLabel(): string
    {
        return 'Formules (offres)';
    }

    public function getDescription(): string
    {
        return 'Liste numérotée des formules proposées : nom, phrase, ce qui est compris, mention de délai ou de prix. Options en bas.';
    }

    public function buildForm(FormBuilderInterface $builder): void
    {
        $builder
            ->add('anchor', TextType::class, ['label' => 'Ancre HTML', 'required' => false, 'help' => 'Ex: formules → lien #formules'])
            ->add('index', TextType::class, ['label' => 'Numéro de section', 'required' => false, 'help' => 'Ex: 02'])
            ->add('title', TextType::class, ['label' => 'Titre', 'required' => false])
            ->add('intro', TextareaType::class, ['label' => 'Intro (à droite du titre)', 'required' => false, 'attr' => ['rows' => 2]])
            ->add('offers', CollectionType::class, [
                'label' => 'Formules',
                'entry_type' => OfferItemType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
                'required' => false,
            ])
            ->add('options_label', TextType::class, ['label' => 'Titre des options', 'required' => false, 'help' => 'Ex : Et tout ce qui va avec'])
            ->add('options', TextareaType::class, [
                'label' => 'Options (une par ligne : nom | description)',
                'required' => false,
                'attr' => ['rows' => 4],
                'help' => 'Ex : SEO | Référencement, données structurées, visibilité locale',
            ])
            ->add('cta_label', TextType::class, ['label' => 'Bouton — libellé', 'required' => false])
            ->add('cta_link', TextType::class, ['label' => 'Bouton — lien ou ancre', 'required' => false, 'help' => 'Ex : #contact']);
    }

    public function getDefaultData(): array
    {
        return [
            'anchor' => 'formules',
            'index' => '',
            'title' => 'Formules',
            'intro' => '',
            'offers' => [
                ['title' => 'Nouvelle formule', 'pitch' => '', 'items' => '', 'note' => ''],
            ],
            'options_label' => 'Et tout ce qui va avec',
            'options' => '',
            'cta_label' => '',
            'cta_link' => '',
        ];
    }
}
