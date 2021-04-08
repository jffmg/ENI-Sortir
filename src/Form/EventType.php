<?php

namespace App\Form;

use App\Entity\Event;
use App\Entity\Location;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

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
                ],
                'constraints' => [],
            ])
            ->add('dateTimeStart', DateTimeType::class, [
                'label' => 'Date de début : ',
                'label_attr' => ['class' => 'app-form-label'],
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'app-form-field app-add-field',
//                    'placeholder' => new \DateTime()
                ],
                'constraints' => [],
            ])
            ->add('duration', IntegerType::class, [
                'label' => 'Durée (minutes) : ',
                'label_attr' => ['class' => 'app-form-label'],
                'attr' => [
                    'class' => 'app-form-field app-add-field',
                ],
                'constraints' => [],
            ])
            ->add('nbInscriptionsMax', IntegerType::class, [
                'label' => 'Nombre de places : ',
                'label_attr' => ['class' => 'app-form-label'],
                'attr' => [
                    'class' => 'app-form-field app-add-field',
                ],
                'constraints' => [],
            ])
            ->add('infosEvent', TextareaType::class, [
                'label' => 'Description : ',
                'label_attr' => ['class' => 'app-form-label'],
                'attr' => [
                    'class' => 'app-form-field app-add-field app-add-textarea-field',
                ],
                'constraints' => [],
            ])
            ->add('dateEndInscription', DateTimeType::class, [
                'label' => 'Date limite d\'inscription : ',
                'widget' => 'single_text',
                'label_attr' => ['class' => 'app-form-label'],
                'attr' => [
                    'class' => 'app-form-field app-add-field',
                ],
                'constraints' => [],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
        ]);
    }
}
