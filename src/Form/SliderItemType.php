<?php

namespace App\Form;

use App\Entity\File;
use App\Entity\SliderItem;
use App\Form\Type\BoolType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SliderItemType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('is_publish', BoolType::class)
            ->add('title')
            ->add('link')
            ->add('image', EntityType::class, [
                'class' => File::class,
                'choice_label' => 'id',
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SliderItem::class,
        ]);
    }
}
