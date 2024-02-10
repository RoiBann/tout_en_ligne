<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class Cart
{
    private $requestStack;
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager, RequestStack $requestStack)
    {
        $this->entityManager = $entityManager;
        $this->requestStack = $requestStack;
    }

    public function add($id)
    {
        $session = $this->requestStack->getSession();
        $cart = $session->get('cart', []);

        if (!empty($cart[$id])){
            $cart[$id]++;
        } else {
            $cart[$id] = 1;
        }
        return $session->set('cart', $cart);
    }

    public function get()
    {
        $session = $this->requestStack->getSession();
        return $session->get('cart');
    }

    public function remove()
    {
        $session = $this->requestStack->getSession();
        return $session->remove('cart');
    }

    public function delete($id)
    {
        $session = $this->requestStack->getSession();
        $cart = $session->get('cart', []);

        unset($cart[$id]);

        return $session->set('cart', $cart);
    }

    public function decrease($id)
    {
        $session = $this->requestStack->getSession();
        $cart = $session->get('cart', []);

        if ($cart[$id] > 1){
            // Retire une quantité
            $cart[$id]--;
        } else {
            // Retire le produit
            unset($cart[$id]);
        }
        return $session->set('cart', $cart);
    }

    public function getFull()
    {
        // Initialise un tableau vide pour stocker les informations complètes du panier
        $cartComplete = [];

        // Vérifie si le panier n'est pas vide en appelant la méthode $this->get()
        if ($this->get()){
            // Itère à travers les éléments du panier
            foreach ($this->get() as $id => $quantity){
                // Recherche l'objet de produit correspondant en utilisant l'EntityManager
                $product_object = $this->entityManager->getRepository(Product::class)->findOneById($id);

                // Si l'objet de produit n'est pas trouvé
                if (!$product_object){
                    // Supprime cet élément du panier
                    $this->delete($id);
                    // Passe à l'élément suivant du panier en l'affichant
                    continue;
                }
                // Si l'objet de produit est trouvé, l'ajoute au tableau $cartComplete avec la quantité correspondante
                $cartComplete[] = [
                    'product' => $product_object,
                    'quantity' => $quantity
                ];
            }
        }
        // Retourne le tableau $cartComplete contenant les informations complètes du panier
        return $cartComplete;
    }
}