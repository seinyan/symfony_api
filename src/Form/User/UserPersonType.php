<?php

namespace App\Form\User;

use App\Entity\File;
use App\Entity\UserPerson;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserPersonType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('image', EntityType::class, [
                'class' => File::class,
                'choice_label' => 'id',
            ])
            ->add('first_name')
            ->add('last_name')
            ->add('middle_name')

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => UserPerson::class,
        ]);
    }
}
