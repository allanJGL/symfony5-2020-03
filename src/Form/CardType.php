<?php

namespace App\Form;

use App\Entity\Card;
use App\Entity\Type;
use App\Entity\User;
use App\Repository\TypeRepository;
use App\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CardType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('health')
            ->add('attack')
            ->add('mana')
            ->add('description')
            ->add('type', EntityType::class, [
                'class' => Type::class,
                'choice_label' => 'name',
            ])
            ->add('save', SubmitType::class, ['label' => 'Create Card'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Card::class,
        ]);
    }
}
