<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class, array(
                'required' => true,
                'label' => 'Nom',
            ))
            ->add('prenom', TextType::class, array(
                'required' => true,
                'label' => 'Prénom',
            ))
            ->add('adresse', TextType::class, array(
                'required' => true,
                'label' => 'Adresse',
            ))
            ->add('telephone', TextType::class, array(
                'required' => true,
                'label' => 'Téléphone',
            ))
        ;

    }

    public function getParent()
    {
        return 'FOS\UserBundle\Form\Type\RegistrationFormType';
    }

    public function getName()
    {
        return 'app_user_registration';
    }
}
