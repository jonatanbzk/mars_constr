<?php

namespace App\Form\Worker;

use App\Entity\Worker;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class WorkerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('first_name', null, [
                'label' => 'Prénom',
            ])
            ->add('last_name', null, [
                'label' => 'Nom',
            ])
            ->add('rank', ChoiceType::class, [
                'choices' => [
                    'N1' => 'N1',
                    'N2' => 'N2',
                    'N3' => 'N3',
                    'N4' => 'N4',
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Ajouter le compagnon',
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Worker::class,
        ]);
    }
}
