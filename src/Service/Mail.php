<?php
declare(strict_types=1);

namespace App\Service;

use Mailjet\Client;
use Mailjet\Resources;

class Mail
{
    private $api_key = 'f6f598864ef43342f684a5da6eb7d251';
    private $api_key_secret = 'feccd86d6abef805b99f2bcda63ee2cc';

    public function send($to_email, $to_name, $subject, $content): void
    {
        $mj = new Client($this->api_key, $this->api_key_secret,true,['version' => 'v3.1']);

        $body = [
            'Messages' => [
                [
                    'From' => [
                        'Email' => "manuchauveau@outlook.fr",
                        'Name' => "La Boutique Bretonne"
                    ],
                    'To' => [
                        [
                            'Email' => $to_email,
                            'Name' => $to_name
                        ]
                    ],
                    'TemplateID' => 1,
                    'TemplateLanguage' => true,
                    'Subject' => $subject,
                    'Variables' => [
                        'content' => $content,
                    ]
                ]
            ]
        ];

        $response = $mj->post(Resources::$Email, ['body' => $body]);
        $response->success() && dd($response->getData());
    }
}