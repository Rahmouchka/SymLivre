<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class ForgotPasswordController extends AbstractController
{
    #[Route('/forgot-password', name: 'app_forgot_password')]
    public function forgotPassword(Request $request, EntityManagerInterface $em, MailerInterface $mailer): Response
    {
        if ($request->isMethod('POST')) {
            $email = $request->request->get('email');
            $user = $em->getRepository(User::class)->findOneBy(['email' => $email]);

            if ($user) {
                $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT); // 6-digit code
                $user->setResetCode($code);
                $em->flush();

                $emailMessage = (new Email())
                    ->from('noreply@example.com')
                    ->to($user->getEmail())
                    ->subject('Password Reset Code')
                    ->text("Your password reset code is: $code");

                $mailer->send($emailMessage);
            }

            return $this->redirectToRoute('app_enter_reset_code');
        }

        return $this->render('security/forgot_password.html.twig');
    }

    #[Route('/enter-reset-code', name: 'app_enter_reset_code')]
    public function enterCode(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $hasher): Response
    {
        if ($request->isMethod('POST')) {
            $email = $request->request->get('email');
            $code = $request->request->get('code');
            $newPassword = $request->request->get('password');

            $user = $em->getRepository(User::class)->findOneBy([
                'email' => $email,
                'resetCode' => $code
            ]);

            if ($user) {
                $user->setPassword($hasher->hashPassword($user, $newPassword));
                $user->setResetCode(null); // Invalidate the code
                $em->flush();

                $this->addFlash('success', 'Password reset successful!');
                return $this->redirectToRoute('app_login');
            } else {
                $this->addFlash('error', 'Invalid email or reset code.');
            }
        }

        return $this->render('security/enter_reset_code.html.twig');
    }
}
