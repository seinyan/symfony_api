<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\ChoiceListInterface;
use Symfony\Component\Form\ChoiceList\Factory\CachingFactoryDecorator;
use Symfony\Component\Form\ChoiceList\Factory\ChoiceListFactoryInterface;
use Symfony\Component\Form\ChoiceList\Factory\DefaultChoiceListFactory;
use Symfony\Component\Form\ChoiceList\Factory\PropertyAccessDecorator;
use Symfony\Component\Form\ChoiceList\View\ChoiceGroupView;
use Symfony\Component\Form\ChoiceList\View\ChoiceListView;
use Symfony\Component\Form\ChoiceList\View\ChoiceView;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\Extension\Core\DataMapper\CheckboxListMapper;
use Symfony\Component\Form\Extension\Core\DataMapper\RadioListMapper;
use Symfony\Component\Form\Extension\Core\DataTransformer\ChoicesToValuesTransformer;
use Symfony\Component\Form\Extension\Core\DataTransformer\ChoiceToValueTransformer;
use Symfony\Component\Form\Extension\Core\EventListener\MergeCollectionListener;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BoolType extends AbstractType
{
    private $choiceListFactory;

    public function __construct(ChoiceListFactoryInterface $choiceListFactory = null)
    {
        $this->choiceListFactory = $choiceListFactory ?: new CachingFactoryDecorator(
            new PropertyAccessDecorator(
                new DefaultChoiceListFactory()
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $choiceList = $this->createChoiceList($options);
//        $builder->setAttribute('choice_list', $choiceList);

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            $data = $event->getData();
            if($data === 'true') {
                $event->setData(true);
            }
            elseif($data === 'false') {
                $event->setData(false);
            }

        }, 256);
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $choiceTranslationDomain = $options['choice_translation_domain'];
        if ($view->parent && null === $choiceTranslationDomain) {
            $choiceTranslationDomain = $view->vars['translation_domain'];
        }

        /** @var ChoiceListInterface $choiceList */
        $choiceList = $form->getConfig()->getAttribute('choice_list');

        /** @var ChoiceListView $choiceListView */
        $choiceListView = $form->getConfig()->hasAttribute('choice_list_view')
            ? $form->getConfig()->getAttribute('choice_list_view')
            : $this->createChoiceListView($choiceList, $options);

        $view->vars = array_replace($view->vars, [
            'multiple' => $options['multiple'],
            'expanded' => $options['expanded'],
            'preferred_choices' => $choiceListView->preferredChoices,
            'choices' => $choiceListView->choices,
            'separator' => '-------------------',
            'placeholder' => null,
            'choice_translation_domain' => $choiceTranslationDomain,
        ]);

        // The decision, whether a choice is selected, is potentially done
        // thousand of times during the rendering of a template. Provide a
        // closure here that is optimized for the value of the form, to
        // avoid making the type check inside the closure.
        if ($options['multiple']) {
            $view->vars['is_selected'] = function ($choice, array $values) {
                return \in_array($choice, $values, true);
            };
        } else {
            $view->vars['is_selected'] = function ($choice, $value) {
                return $choice === $value;
            };
        }

        // Check if the choices already contain the empty value
        $view->vars['placeholder_in_choices'] = $choiceListView->hasPlaceholder();

        // Only add the empty value option if this is not the case
        if (null !== $options['placeholder'] && !$view->vars['placeholder_in_choices']) {
            $view->vars['placeholder'] = $options['placeholder'];
        }

        if ($options['multiple'] && !$options['expanded']) {
            // Add "[]" to the name in case a select tag with multiple options is
            // displayed. Otherwise only one of the selected options is sent in the
            // POST request.
            $view->vars['full_name'] .= '[]';
        }
    }

    /**
     * {@inheritdoc}
     */
    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        if ($options['expanded']) {
            // Radio buttons should have the same name as the parent
            $childName = $view->vars['full_name'];

            // Checkboxes should append "[]" to allow multiple selection
            if ($options['multiple']) {
                $childName .= '[]';
            }

            foreach ($view as $childView) {
                $childView->vars['full_name'] = $childName;
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $emptyData = function (Options $options) {
            if ($options['expanded'] && !$options['multiple']) {
                return null;
            }

            if ($options['multiple']) {
                return [];
            }

            return '';
        };

        $placeholderDefault = function (Options $options) {
            return $options['required'] ? null : '';
        };

        $placeholderNormalizer = function (Options $options, $placeholder) {
            if ($options['multiple']) {
                // never use an empty value for this case
                return null;
            } elseif ($options['required'] && ($options['expanded'] || isset($options['attr']['size']) && $options['attr']['size'] > 1)) {
                // placeholder for required radio buttons or a select with size > 1 does not make sense
                return null;
            } elseif (false === $placeholder) {
                // an empty value should be added but the user decided otherwise
                return null;
            } elseif ($options['expanded'] && '' === $placeholder) {
                // never use an empty label for radio buttons
                return 'None';
            }

            // empty value has been set explicitly
            return $placeholder;
        };

        $compound = function (Options $options) {
            return $options['expanded'];
        };

        $choiceTranslationDomainNormalizer = function (Options $options, $choiceTranslationDomain) {
            if (true === $choiceTranslationDomain) {
                return $options['translation_domain'];
            }

            return $choiceTranslationDomain;
        };

        $resolver->setDefaults([
            'multiple' => false,
            'expanded' => false,
            'choices' => [],
            'choice_loader' => null,
            'choice_label' => null,
            'choice_name' => null,
            'choice_value' => null,
            'choice_attr' => null,
            'preferred_choices' => [],
            'group_by' => null,
            'empty_data' => $emptyData,
            'placeholder' => $placeholderDefault,
            'error_bubbling' => false,
            'compound' => $compound,
            // The view data is always a string or an array of strings,
            // even if the "data" option is manually set to an object.
            // See https://github.com/symfony/symfony/pull/5582
            'data_class' => null,
            'choice_translation_domain' => true,
            'trim' => false,
        ]);

        $resolver->setNormalizer('placeholder', $placeholderNormalizer);
        $resolver->setNormalizer('choice_translation_domain', $choiceTranslationDomainNormalizer);

        $resolver->setAllowedTypes('choices', ['null', 'array', '\Traversable']);
        $resolver->setAllowedTypes('choice_translation_domain', ['null', 'bool', 'string']);
        $resolver->setAllowedTypes('choice_loader', ['null', 'Symfony\Component\Form\ChoiceList\Loader\ChoiceLoaderInterface']);
        $resolver->setAllowedTypes('choice_label', ['null', 'bool', 'callable', 'string', 'Symfony\Component\PropertyAccess\PropertyPath']);
        $resolver->setAllowedTypes('choice_name', ['null', 'callable', 'string', 'Symfony\Component\PropertyAccess\PropertyPath']);
        $resolver->setAllowedTypes('choice_value', ['null', 'callable', 'string', 'Symfony\Component\PropertyAccess\PropertyPath']);
        $resolver->setAllowedTypes('choice_attr', ['null', 'array', 'callable', 'string', 'Symfony\Component\PropertyAccess\PropertyPath']);
        $resolver->setAllowedTypes('preferred_choices', ['array', '\Traversable', 'callable', 'string', 'Symfony\Component\PropertyAccess\PropertyPath']);
        $resolver->setAllowedTypes('group_by', ['null', 'callable', 'string', 'Symfony\Component\PropertyAccess\PropertyPath']);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'choice';
    }

    /**
     * Adds the sub fields for an expanded choice field.
     */
    private function addSubForms(FormBuilderInterface $builder, array $choiceViews, array $options)
    {
        foreach ($choiceViews as $name => $choiceView) {
            // Flatten groups
            if (\is_array($choiceView)) {
                $this->addSubForms($builder, $choiceView, $options);
                continue;
            }

            if ($choiceView instanceof ChoiceGroupView) {
                $this->addSubForms($builder, $choiceView->choices, $options);
                continue;
            }

            $this->addSubForm($builder, $name, $choiceView, $options);
        }
    }

    private function addSubForm(FormBuilderInterface $builder, string $name, ChoiceView $choiceView, array $options)
    {
        $choiceOpts = [
            'value' => $choiceView->value,
            'label' => $choiceView->label,
            'attr' => $choiceView->attr,
            'translation_domain' => $options['translation_domain'],
            'block_name' => 'entry',
        ];

        if ($options['multiple']) {
            $choiceType = CheckboxType::class;
            // The user can check 0 or more checkboxes. If required
            // is true, they are required to check all of them.
            $choiceOpts['required'] = false;
        } else {
            $choiceType = RadioType::class;
        }

        $builder->add($name, $choiceType, $choiceOpts);
    }

    private function createChoiceList(array $options)
    {
        if (null !== $options['choice_loader']) {
            return $this->choiceListFactory->createListFromLoader(
                $options['choice_loader'],
                $options['choice_value']
            );
        }

        // Harden against NULL values (like in EntityType and ModelType)
        $choices = null !== $options['choices'] ? $options['choices'] : [];

        return $this->choiceListFactory->createListFromChoices($choices, $options['choice_value']);
    }

    private function createChoiceListView(ChoiceListInterface $choiceList, array $options)
    {
        return $this->choiceListFactory->createView(
            $choiceList,
            $options['preferred_choices'],
            $options['choice_label'],
            $options['choice_name'],
            $options['group_by'],
            $options['choice_attr']
        );
    }
}
