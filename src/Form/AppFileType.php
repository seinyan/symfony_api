<?php

namespace App\Form;

use App\Entity\File;
use App\Form\Type\BoolType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AppFileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('file', FileType::class, [
                'label' => ' ',
                'mapped' => false,
                'required' => false,
            ])
            ->add('is_private', BoolType::class)
            ->add('title')
            ->add('sub_path')
            ->add('x')
            ->add('y')
            ->add('width')
            ->add('height')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => File::class,
        ]);
    }
}
