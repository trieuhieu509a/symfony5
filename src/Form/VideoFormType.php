<?php

namespace App\Form;

use App\Entity\Video;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VideoFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
//            ->add('filename')
//            ->add('size')
//            ->add('description')
            ->add('title', TextType::class, [
                'label' => 'Set video title',
                'data' => 'Example title',
                'required' => false,
            ])
//            ->add('format')
//            ->add('duration')
            ->add('created_at', DateType::class, [
                'label' => 'Set date',
                'widget' => 'single_text',
            ])
//            ->add('author')
//            ->add('user')
            ->add('save', SubmitType::class, ['label' => 'Add a video'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Video::class,
        ]);
    }
}