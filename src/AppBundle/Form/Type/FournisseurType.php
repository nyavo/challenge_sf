<?php
/**
 * Created by PhpStorm.
 * User: Ny Avo
 * Date: 18/04/2018
 * Time: 10:59
 */
namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class FournisseurType
 */
class FournisseurType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nomFournisseur', TextType::class, array(
                'required' => true,
                'label' => 'Nom Fournisseur',
            ))
            ->add('save', SubmitType::class)
        ;
    }

}
