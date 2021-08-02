<?php

namespace App\Form;

use App\Entity\File;
use App\Entity\News;
use App\Form\Type\BoolType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NewsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
//            ->add('slug')
            ->add('is_publish', BoolType::class)
            ->add('publish_at', DateType::class, [
                'widget' => 'single_text',
                'format' => 'dd-MM-yyyy HH:mm',
                'html5' => false,
            ])
            ->add('title')
            ->add('description')
            ->add('image', EntityType::class, [
                'class' => File::class,
                'choice_label' => 'id',
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => News::class,
        ]);
    }
}
