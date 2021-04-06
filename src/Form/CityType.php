<?php

namespace App\Form;

use App\Entity\City;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CityType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => false,
                'label_attr' => ['class' => 'app-form-label'],
                'attr' => [
                    'class' => 'app-form-field app-update-field',
                    'placeholder' => 'Nom',
                    'required' => false,
                ]
            ])
            ->add('zipCode', TextType::class, [
                'label' => false,
                'label_attr' => ['class' => 'app-form-label'],
                'attr' => [
                    'class' => 'app-form-field app-update-field',
                    'placeholder' => 'Code postal',
                    'required' => false,
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => City::class,
            'attr' => ['id' => 'newCityForm'],
        ]);
    }
}
