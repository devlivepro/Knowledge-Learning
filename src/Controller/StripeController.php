<?php

namespace App\Controller;

use App\Entity\Order;
use App\Repository\UserRepository;
use App\Repository\CursusRepository;
use App\Repository\LessonRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Stripe\Webhook;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class StripeController extends AbstractController
{
    #[Route('/stripe/webhook', name: 'stripe_webhook')]
    public function stripeWebhook(
        Request $request,
        EntityManagerInterface $em,
        UserRepository $userRepo,
        CursusRepository $cursusRepo,
        LessonRepository $lessonRepo
    ): Response {
        $payload = $request->getContent();
        $sigHeader = $request->headers->get('stripe-signature');

        try {
            $event = Webhook::constructEvent(
                $payload,
                $sigHeader,
                $_ENV['STRIPE_WEBHOOK_SECRET']
            );
        } catch (\Exception $e) {
            return new Response('Webhook error', 400);
        }

        if ($event->type === 'checkout.session.completed') {
            file_put_contents('stripe_debug.log', $payload);
            $session = $event->data->object;

            $user = $userRepo->find($session->metadata->user_id);
            $type = $session->metadata->type;
            $targetId = $session->metadata->target_id;

            $order = new Order();
            $order->setUser($user)
                  ->setTotal($session->amount_total / 100)
                  ->setStatus('Payé')
                  ->setCreatedAt(new \DateTimeImmutable());

            if ($type === 'cursus') {
                $cursus = $cursusRepo->find($targetId);
                $order->setCursus($cursus);
            } elseif ($type === 'lesson') {
                $lesson = $lessonRepo->find($targetId);
                $order->setLesson($lesson);
            }

            $em->persist($order);
            $em->flush();
        }

        return new Response('OK', 200);
    }

    #[Route('/payment/success', name: 'payment_success')]
    public function paymentSuccess(): Response
    {
        $this->addFlash('success', '✅ Paiement validé ! Vous recevrez vos accès sous peu.');
        return $this->redirectToRoute('student_courses');
    }

    #[Route('/payment/cancel', name: 'payment_cancel')]
    public function paymentCancel(): Response
    {
        $this->addFlash('error', '❌ Paiement annulé. Vous pouvez réessayer quand vous le souhaitez.');
        return $this->redirectToRoute('student_shop');
    }
}