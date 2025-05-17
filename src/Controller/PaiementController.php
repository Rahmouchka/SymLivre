<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PaiementController extends AbstractController
{
    #[Route('/stripe', name: 'app_stripe')]
    public function index(): Response
    {
        return $this->render('paiement/paiement.html.twig', [
            'stripe_public_key' => $_ENV['STRIPE_PUBLIC_KEY'],
        ]);
    }

    #[Route('/create-checkout-session', name: 'create_checkout_session', methods: ['POST'])]
    public function createCheckoutSession(): JsonResponse
    {
        \Stripe\Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);

        $session = \Stripe\Checkout\Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => 'Livre',
                    ],
                    'unit_amount' => 2000,
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => $this->generateUrl('payment_success', [], UrlGeneratorInterface::ABSOLUTE_URL),
            'cancel_url' => $this->generateUrl('payment_cancel', [], UrlGeneratorInterface::ABSOLUTE_URL),
        ]);

        return new JsonResponse(['id' => $session->id]);
    }

    #[Route('/payment-success', name: 'payment_success')]
    public function success(): Response
    {
        return $this->render('stripe/success.html.twig');
    }

    #[Route('/payment-cancel', name: 'payment_cancel')]
    public function cancel(): Response
    {
        return $this->render('stripe/cancel.html.twig');
    }
}
