<?php

namespace Trikoder\JsonApiBundle\Tests\Resources\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Trikoder\JsonApiBundle\Tests\Resources\Model\ContactInfoModel;

class ContactInfoTypeWithGroup extends AbstractType
{
    /**
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('phoneNumber', PhoneNumberType::class);
        $builder->add('label', TextType::class);
    }

    /**
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ContactInfoModel::class,
            'validation_groups' => 'alwaysInvalidPhoneNumber',
        ]);
    }
}
