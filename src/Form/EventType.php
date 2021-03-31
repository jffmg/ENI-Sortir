<?php

namespace App\Form;

use App\Entity\Event;
use App\Entity\Location;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom : ',
                'label_attr' => ['class' => 'app-form-label'],
                'attr' => [
                    'class' => 'app-form-field app-add-field',
                ]
            ])
            ->add('dateTimeStart', DateTimeType::class, [
                'label' => 'Date de début : ',
                'label_attr' => ['class' => 'app-form-label'],
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'app-form-field app-add-field',
//                    'placeholder' => new \DateTime()
                ]
            ])
            ->add('duration', IntegerType::class, [
                'label' => 'Durée (minutes) : ',
                'label_attr' => ['class' => 'app-form-label'],
                'attr' => [
                    'class' => 'app-form-field app-add-field',
                ]
            ])
            ->add('nbInscriptionsMax', IntegerType::class, [
                'label' => 'Nombre maximal de participants : ',
                'label_attr' => ['class' => 'app-form-label'],
                'attr' => [
                    'class' => 'app-form-field app-add-field',
                ],
            ])
            ->add('infosEvent', TextareaType::class, [
                'label' => 'Description : ',
                'label_attr' => ['class' => 'app-form-label'],
                'attr' => [
                    'class' => 'app-form-field app-add-field',
                ]
            ])
            ->add('dateEndInscription', DateTimeType::class, [
                'widget' => 'single_text',
                'label' => 'Date limite d\'inscription : ',
                'label_attr' => ['class' => 'app-form-label'],
                'attr' => [
                    'class' => 'app-form-field app-add-field',
                ]
            ])
//            ->add('location', EntityType::class, [
//                'label' => 'Lieu : ',
//                'class' => Location::class,
//                'label_attr'=> ['class'=> 'app-form-label'],
//                'attr' => [
//                    'class' => 'app-form-field app-add-field',
//                ]
//            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
        ]);
    }
}
