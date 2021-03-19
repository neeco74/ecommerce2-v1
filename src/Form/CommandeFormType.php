<?php

namespace App\Form;

use App\Entity\Address;
use App\Entity\Transporteur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class CommandeFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        
        $user = $options['user'];
        
        $builder
            ->add('addresses', EntityType::class, [
                'label' => false,
                'required' => true,
                'class' => Address::class,
                'choices' => $user->getAddresses(),
                'multiple' => false,
                'expanded' => true,
            ])
            ->add('transporteur', EntityType::class, [
                'label' => 'Choisissez votre transporteur',
                'required' => true,
                'class' => Transporteur::class,
                'multiple' => false,
                'expanded' => true,
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Valider ma commande',
                'attr' => [
                    'class' => 'btn btn-block btn-info'
                ]
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
           'user' => []
        ]);
    }
}
