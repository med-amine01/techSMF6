<?php

namespace App\Form;

use App\Entity\Personne;
use App\Entity\Societe;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PersonneType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstname')
            ->add('age')
            ->add('lastname')
            ->add('societe',EntityType::class,[
                'class'=>Societe::class,
//                'expanded'=>false,
//                'required'=>false,
                'multiple'=>false,
                'attr'=>[
                    'class'=>'select2'
                ]
            ])
            ->add('Add',SubmitType::class)
            ->add('Edit',SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Personne::class,
        ]);
    }
}
