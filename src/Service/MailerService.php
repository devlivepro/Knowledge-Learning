<?php

namespace App\Service;

use Mailjet\Client;
use Mailjet\Resources;

class MailerService
{
    private Client $client;

    public function __construct(string $mailjetApiKey, string $mailjetApiSecret)
    {
        $this->client = new Client($mailjetApiKey, $mailjetApiSecret, true, ['version' => 'v3.1']);
    }

    // How to send the verification email
    public function sendVerificationEmail(string $toEmail, string $verificationLink)
    {
        $body = [
            'Messages' => [
                [
                    'From' => [
                        'Email' => "knowledgelearningproject@gmail.com",
                        'Name' => "Knowledge Learning"
                    ],
                    'To' => [
                        [
                            'Email' => $toEmail,
                            'Name' => "Cher utilisateur"
                        ]
                    ],
                    'Subject' => "Confirmez votre compte",
                    'HTMLPart' => "<h3>Bienvenue sur Knowledge Learning!</h3><p>Pour confirmer votre compte, veuillez cliquer sur ce lien : <a href='{$verificationLink}'>Confirmer mon compte</a></p>",
                ]
            ]
        ];

        $response = $this->client->post(Resources::$Email, ['body' => $body]);
        return $response->success();
    }

    // New method for sending password reset email
    public function sendResetPasswordEmail(string $toEmail, string $resetUrl)
    {
        $body = [
            'Messages' => [
                [
                    'From' => [
                        'Email' => "knowledgelearningproject@gmail.com",
                        'Name' => "Knowledge Learning"
                    ],
                    'To' => [
                        [
                            'Email' => $toEmail,
                            'Name' => "Cher utilisateur"
                        ]
                    ],
                    'Subject' => "Réinitialisation de votre mot de passe",
                    'HTMLPart' => "<h3>Réinitialisation du mot de passe</h3><p>Pour réinitialiser votre mot de passe, veuillez cliquer sur ce lien : <a href='{$resetUrl}'>Réinitialiser mon mot de passe</a></p>",
                ]
            ]
        ];

        $response = $this->client->post(Resources::$Email, ['body' => $body]);
        return $response->success();
    }
}