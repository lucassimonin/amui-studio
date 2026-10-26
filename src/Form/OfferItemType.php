<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OfferItemType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, ['label' => 'Nom de la formule', 'required' => false, 'help' => 'Ex : Press kit'])
            ->add('pitch', TextType::class, ['label' => 'En une phrase', 'required' => false])
            ->add('items', TextareaType::class, [
                'label' => 'Ce qui est compris (un par ligne)',
                'required' => false,
                'attr' => ['rows' => 4],
                'help' => "Ex : Une page prête à envoyer\nPresse et dates\nMise en ligne incluse",
            ])
            ->add('note', TextType::class, ['label' => 'Mention (délai, tarif…)', 'required' => false, 'help' => 'Ex : 1 semaine — à partir de 900 €']);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => null]);
    }
}
