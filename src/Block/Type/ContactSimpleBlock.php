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
            ->add('index', TextType::class, ['label' => 'Numéro de section', 'required' => false, 'help' => 'Ex: 03 — reprend la numérotation du menu'])
            ->add('title', TextType::class, ['label' => 'Titre', 'required' => false])
            ->add('text', TextareaType::class, ['label' => 'Texte', 'required' => false, 'attr' => ['rows' => 3]])
            ->add('infos_label', TextType::class, ['label' => 'Libellé au-dessus de l\'email', 'required' => false, 'help' => 'Ex : Écrivez-nous'])
            ->add('email', TextType::class, ['label' => 'Email affiché', 'required' => false])
            ->add('phone', TextType::class, ['label' => 'Téléphone affiché', 'required' => false])
            ->add('form_note', TextType::class, ['label' => 'Note sous le formulaire', 'required' => false])
            ->add('recipient', TextType::class, ['label' => 'Email destinataire des demandes', 'required' => false, 'help' => 'Vide = email de contact des réglages généraux']);
    }

    public function getDefaultData(): array
    {
        return [
            'anchor' => 'contact',
            'index' => '',
            'title' => 'Contact',
            'text' => '',
            'infos_label' => 'Écrivez-nous',
            'email' => '',
            'phone' => '',
            'form_note' => 'En envoyant, vous acceptez un retour par email.',
            'recipient' => '',
        ];
    }
}
