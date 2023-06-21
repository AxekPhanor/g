<?php

namespace App\ClassPhp;
use Mailjet\Client;
use Mailjet\Resources;


class Mail
{
    private $api_key = '60e621eae850166c5439fdbd965b688c';
    private $api_secret = 'efa1c28b85d5f3a9ddb726858c077e6e';

    public function send($toEmail, $toName, $subject, $textPart, $htmlPart)
    {
        $mj = new Client(getenv($this->api_key), getenv($this->api_secret), true,['version' => 'v3.1'], $subject);
        $body = [
            'Messages' => [
            [
                'From' => [
                'Email' => "axel.phanor64@gmail.com", // TODO: A MODIFIER PAR UN NOM DE DOMAINE
                'Name' => "Les gateaux de la madre"
                ],
                'To' => [
                [
                    'Email' => $toEmail,
                    'Name' => $toName
                ]
                ],
                'Subject' => $subject,
                'TextPart' => $textPart,
                'HTMLPart' => $htmlPart,
            ]
            ]
        ];
        $response = $mj->post(Resources::$Email, ['body' => $body]);
        $response->success() && var_dump($response->getData());
    }
}