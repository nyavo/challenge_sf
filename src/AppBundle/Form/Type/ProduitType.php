<?php
/**
 * Created by PhpStorm.
 * User: nyavo
 * Date: 17/04/18
 * Time: 23:27
 */
namespace AppBundle\Form\Type;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class ProduitType
 */
class ProduitType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('titre', TextType::class, array(
                'required' => true,
                'label' => 'Titre',
            ))
            ->add('description', TextareaType::class, array(
                'required' => true,
                'label' => 'Description',
            ))
            ->add('stock', IntegerType::class, array(
                'required' => true,
                'label' => 'Quantité en stock',
            ))
            ->add('prix', NumberType::class, array(
                'required' => true,
                'label' => 'Prix',
            ))
            ->add('genre', ChoiceType::class, array(
                'required' => true,
                'choices' => array(
                    'homme' => 'homme',
                    'femme' => 'femme',
                    'mixte' => 'mixte',
                    'enfant' => 'enfant',
                ),
                'label' => 'Genre',
            ))
            ->add('type', ChoiceType::class, array(
                'required' => true,
                'choices' => array(
                    'soleil' => 'soleil',
                    'vue' => 'vue',
                    'sport' => 'sport',
                ),
                'label' => 'Type',
            ))
            ->add('fournisseur', EntityType::class, array(
                'class' => 'AppBundle:Fournisseur',
                'choice_label' => 'nomFournisseur',
                'label' => 'Fournisseur',
                'required' => true,
            ))
            ->add('save', SubmitType::class);
        ;
    }

}
