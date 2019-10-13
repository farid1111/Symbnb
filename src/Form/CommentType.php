<?php

namespace App\Form;

use App\Entity\Comment;
use App\Form\ApplicationType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class CommentType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('content',TextareaType::class,$this->getConfiguration("Commentaire","Laissez un commentaire"))
            ->add('rating', IntegerType::class, $this->getConfiguration("Notez votre location","Veuillez notez votre location",[
                'attr'=> [
                    'min'=> 0,
                    'max'=> 5,
                    'step'=> 1
                ]
            ]))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Comment::class,
            'validation_groups'=>[
                'Default',
                'front'
            ]
        ]);
    }
}
