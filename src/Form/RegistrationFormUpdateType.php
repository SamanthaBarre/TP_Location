<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegistrationFormUpdateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('civilite', ChoiceType::class, ['choices' => [ 'Femme' => 'F', 'Homme' => 'M']])
        ->add('pseudo')
        ->add('prenom')
        ->add('nom')
            ->add('email')
            ->add('role', ChoiceType::class, ["mapped" => false, "required" => false, "choices" => ["Membre" => "ROLE_MEMBRE", "Admin" => "ROLE_ADMIN"], "placeholder" => "--choisir--"])
            ->add('plainPassword', PasswordType::class, ["mapped" => false, "required" => false, "label" => "Laisser vide pour ne pas changer le mot de passe !"])
                 
            
            ->add('save', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
