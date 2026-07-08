<?php

namespace App\Block\Type;

use App\Block\AbstractBlockType;
use App\Form\ProjectItemType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class ProjectGridBlock extends AbstractBlockType
{
    public function getKey(): string
    {
        return 'project_grid';
    }

    public function getLabel(): string
    {
        return 'Grille de projets';
    }

    public function getDescription(): string
    {
        return 'Cartes cliquables : image, nom du projet et tags.';
    }

    public function buildForm(FormBuilderInterface $builder): void
    {
        $builder
            ->add('anchor', TextType::class, ['label' => 'Ancre HTML', 'required' => false, 'help' => 'Ex: projets → lien #projets dans la navigation'])
            ->add('title', TextType::class, ['label' => 'Titre', 'required' => false])
            ->add('projects', CollectionType::class, [
                'label' => 'Projets',
                'entry_type' => ProjectItemType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
                'required' => false,
            ]);
    }

    public function getDefaultData(): array
    {
        return [
            'anchor' => 'projets',
            'title' => 'Projets',
            'projects' => [
                ['image' => '', 'image_alt' => '', 'title' => 'Nouveau projet', 'meta' => '', 'url' => '#'],
            ],
        ];
    }
}
