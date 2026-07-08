<?php

namespace App\Block\Type;

use App\Block\AbstractBlockType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class ContactSimpleBlock extends AbstractBlockType
{
    public function getKey(): string
    {
        return 'contact_simple';
    }

    public function getLabel(): string
    {
        return 'Contact (infos + formulaire)';
    }

    public function getDescription(): string
    {
        return 'Coordonnées à gauche, formulaire minimaliste à droite, anti-spam intégré.';
    }

    public function buildForm(FormBuilderInterface $builder): void
    {
        $builder
            ->add('anchor', TextType::class, ['label' => 'Ancre HTML', 'required' => false, 'help' => 'Ex: contact → lien #contact'])
            ->add('title', TextType::class, ['label' => 'Titre', 'required' => false])
            ->add('text', TextareaType::class, ['label' => 'Texte', 'required' => false, 'attr' => ['rows' => 3]])
            ->add('infos_label', TextType::class, ['label' => 'Libellé de l\'encadré infos', 'required' => false])
            ->add('email', TextType::class, ['label' => 'Email affiché', 'required' => false])
            ->add('phone', TextType::class, ['label' => 'Téléphone affiché', 'required' => false])
            ->add('form_note', TextType::class, ['label' => 'Note sous le formulaire', 'required' => false])
            ->add('recipient', TextType::class, ['label' => 'Email destinataire des demandes', 'required' => false, 'help' => 'Vide = email de contact des réglages généraux']);
    }

    public function getDefaultData(): array
    {
        return [
            'anchor' => 'contact',
            'title' => 'Contact',
            'text' => '',
            'infos_label' => 'Infos',
            'email' => '',
            'phone' => '',
            'form_note' => 'En envoyant, vous acceptez un retour par email.',
            'recipient' => '',
        ];
    }
}
