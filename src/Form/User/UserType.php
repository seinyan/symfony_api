<?php

namespace App\Form\User;

use App\AppConsts;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email')
            ->add('phone')
//            ->add('password')
            ->add('role')
            ->add('person', UserPersonType::class)
        ;

        $builder->addEventListener(FormEvents::PRE_SUBMIT, [$this, 'submit']);
    }



    public function submit(FormEvent $event): void
    {
        $form = $event->getForm();
        $data = $event->getData();

        if($form->getConfig()->getMethod() === 'POST') {
            $form
                ->add('role', ChoiceType::class, [
                    'choices'  => [
                        User::ROLE_USER    => User::ROLE_USER,
                        User::ROLE_ADMIN   => User::ROLE_ADMIN,
                        User::ROLE_MANAGER => User::ROLE_MANAGER,
                    ]
                ]);
        }


//        if ($data['role'] === User::ROLE_EXECUTOR) {
//           // $form->add('executor', ExecutorType::class);
//        } else {
//            if(array_key_exists('executor', $data)){
////                unset($data['executor']);
////                $event->setData($data);
//            }
//        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
