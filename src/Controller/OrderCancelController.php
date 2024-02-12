<?php

namespace App\Controller;

use App\Entity\Order;
use App\Service\Mail;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderCancelController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }


    /**
     * @Route("/commande/erreur/{stripeSessionId}", name="order_cancel")
     */
    public function index($stripeSessionId): Response
    {
        $order = $this->entityManager->getRepository(Order::class)->findOneByStripeSessionId($stripeSessionId);

        if (!$order || $order->getUser() != $this->getUser()){
            return  $this->redirectToRoute('home');
        }

        //Envoyer un email à notre utilisateur pour lui indiquer un echec de paiement
        $mail = new Mail();
        $content = "Bonjour ".$order->getUser()->getFirstname()."<br>Merci pour votre commande sur <strong>La boutique Bretonne</strong>.<br><br>Vous serez à  coup sûr satisfait(e) de votre passage sur votre boutique en ligne préférée!";
        $mail->send($order->getUser()->getEmail(), $order->getUser()->getFirstname(), "Votre commande à  bien été validée!", $content);

        return $this->render('order_cancel/index.html.twig', [
            'order' => $order
        ]);
    }
}