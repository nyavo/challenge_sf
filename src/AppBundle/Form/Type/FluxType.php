<?php
/**
 * Created by PhpStorm.
 * User: Ny Avo
 * Date: 18/04/2018
 * Time: 15:25
 */
namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class FluxType
 */
class FluxType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('date', DateTimeType::class, array(
                'required' => true,
                'label' => 'Date',
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy',
                'model_timezone' => 'UTC',
                'view_timezone' => 'UTC',
            ))
            ->add('qte', IntegerType::class, array(
                'required' => true,
                'label' => 'Quantité',
            ))
            ->add('save', SubmitType::class)
        ;
    }

}
