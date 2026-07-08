<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProjectItemType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('image', TextType::class, ['label' => 'Image (URL)', 'required' => false])
            ->add('image_alt', TextType::class, ['label' => 'Texte alternatif', 'required' => false])
            ->add('title', TextType::class, ['label' => 'Nom du projet', 'required' => false])
            ->add('meta', TextType::class, ['label' => 'Tags', 'required' => false, 'help' => 'Ex: Identité · Site · SEO'])
            ->add('url', TextType::class, ['label' => 'Lien', 'required' => false, 'help' => 'URL du projet ou # si aucune']);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => null]);
    }
}
