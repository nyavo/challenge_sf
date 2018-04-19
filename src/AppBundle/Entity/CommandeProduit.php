<?php
/**
 * Created by PhpStorm.
 * User: Ny Avo
 * Date: 19/04/2018
 * Time: 18:05
 */
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class CommandeProduit
 *
 * @ORM\Table(name="commande_produit")
 * @ORM\Entity()
 */
class CommandeProduit
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Produit", inversedBy="commandes")
     * @ORM\JoinColumn(name="produit_id", referencedColumnName="id", nullable=false)
     */
    private $produit;

    /**
     * @ORM\ManyToOne(targetEntity="Commande", inversedBy="produits")
     * @ORM\JoinColumn(name="commande_id", referencedColumnName="id", nullable=false)
     */
    private $commande;

    /**
     * @var int
     *
     * @ORM\Column(name="quantite_commande", type="integer", nullable=false)
     */
    private $quantiteCommande;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getProduit()
    {
        return $this->produit;
    }

    /**
     * @param mixed $produit
     */
    public function setProduit($produit)
    {
        $this->produit = $produit;
    }

    /**
     * @return mixed
     */
    public function getCommande()
    {
        return $this->commande;
    }

    /**
     * @param mixed $commande
     */
    public function setCommande($commande)
    {
        $this->commande = $commande;
    }

    /**
     * @return int
     */
    public function getQuantiteCommande()
    {
        return $this->quantiteCommande;
    }

    /**
     * @param int $quantiteCommande
     */
    public function setQuantiteCommande($quantiteCommande)
    {
        $this->quantiteCommande = $quantiteCommande;
    }
}