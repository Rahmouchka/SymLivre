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
            'stripe_public_key' => 'pk_test_51RNd4wADgS5ZYWcqrY4yvnoIDVwERv839qmOAz04XJLBMmJkh1g98Y40fZCk0BHsx85jn1K3bGaG4icEoSIsyFa000YEMrvqi3',
        ]);
    }

    #[Route('/create-checkout-session', name: 'create_checkout_session', methods: ['POST'])]
    public function createCheckoutSession(): JsonResponse
    {
        \Stripe\Stripe::setApiKey('sk_test_51RNd4wADgS5ZYWcqYpsjDqplZA7bO7Fjm7hs7PX2aHGtejb9AkE78EpBq3biSd9Jx2Z9kWNImRgkNcokJRUYqJgM00U6k55Ueb');

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
