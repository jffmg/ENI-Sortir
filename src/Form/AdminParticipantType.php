<?php

namespace App\Form;

use App\Entity\Participant;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

class AdminParticipantType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('userName', TextType::class, [
                'label' => 'Pseudo : ',
                'label_attr' => ['class' => 'app-form-label'],
                'attr' => [
                    'class' => 'app-form-field app-update-field',
                ]
            ])
            ->add('name', TextType::class, [
                'label' => 'Nom : ',
                'label_attr' => ['class' => 'app-form-label'],
                'attr' => [
                    'class' => 'app-form-field app-update-field',
                ]
            ])
            ->add('firstName', TextType::class, [
                'label' => 'Prénom : ',
                'label_attr' => ['class' => 'app-form-label'],
                'attr' => [
                    'class' => 'app-form-field app-update-field',
                ]
            ])
            ->add('phone', TextType::class, [
                'label' => 'Téléphone : ',
                'label_attr' => ['class' => 'app-form-label'],
                'attr' => [
                    'class' => 'app-form-field app-update-field',
                ],
                'required' => false,
            ])
            ->add('mail', TextType::class, [
                'label' => 'Mail : ',
                'label_attr' => ['class' => 'app-form-label'],
                'attr' => [
                    'class' => 'app-form-field app-update-field',
                ]
            ])
            ->add('pictureFile', VichImageType::class, [
                'label' => 'Photo de profil : ',
                'label_attr' => ['class' => 'app-form-label'],
                'required' => false,
                'allow_delete' => false,
                /*'delete_label' => 'Supprimer',*/
                /*'download_label' => 'Télécharger',*/
                'download_uri' => false,
                'image_uri' => false,
                'asset_helper' => true,
                'attr' => [
                    'class' => 'app-form-field app-update-field',
                ]
            ])
            ->add('password', HiddenType::class, [
                'required' => false,
            ])
            /*->add('admin', CheckboxType::class, [
                'label' => 'Cochez si le profil est administrateur :',
                'label_attr' => ['class' => 'app-form-label'],
                'attr' => [
                    'class' => 'app-form-field app-update-field',
                ]
            ])*/
            ->add('admin', ChoiceType::class, [
                'label' => 'Compte administrateur : ',
                'choices' => ['Oui' => 1, 'Non' => 0],
                'expanded' => true,
                'multiple' => false,
                'label_attr' => ['class' => 'app-form-label'],
                'attr' => [
                    'class' => 'app-form-field app-update-field',
                ]
            ])
                    ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Participant::class,
        ]);
    }
}
