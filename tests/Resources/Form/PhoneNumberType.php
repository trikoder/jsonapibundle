<?php

namespace Trikoder\JsonApiBundle\Tests\Resources\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Trikoder\JsonApiBundle\Tests\Resources\Model\PhoneNumberModel;

class PhoneNumberType extends AbstractType
{
    /**
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('areaCode', TextType::class);
        $builder->add('number', TextType::class);
        $builder->add('intNumber', IntegerType::class);
        $builder->add('numberWithValidationOnlyOnForm', TextType::class, [
            'constraints' => [
                new Length(['min' => 3]),
            ],
        ]);
    }

    /**
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => PhoneNumberModel::class,
        ]);
    }
}
