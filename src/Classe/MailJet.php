<?php

namespace App\Classe;

use Mailjet\Client;
use Mailjet\Resources;



class MailJet
{

    /*
    * @var string
    */
    private $api_key = '2b147f31af797f41509f60655d4229b7';

    private $api_key_secret = '37bcdef47460254089195fdb2c8ee0aa';


    public function send($to_email, $to_name, $subject, $content) {

        $mj = new Client($this->api_key, $this->api_key_secret, true,['version' => 'v3.1']);
        //$mj = new \Mailjet\Client(getenv('MJ_APIKEY_PUBLIC'), getenv('MJ_APIKEY_PRIVATE'),true,['version' => 'v3.1']);
        $body = [
            'Messages' => [
                [
                    'From' => [
                        'Email' => "o.nico74@hotmail.fr",
                        'Name' => "La boutique FR"
                    ],
                    'To' => [
                        [
                            'Email' => $to_email,
                            'Name' => $to_name
                        ]
                    ],
                    'TemplateID' => 2672234,
                    'TemplateLanguage' => true,
                    'Subject' => $subject,
                    'Variables' => [
                        'content' => $content
                    ]
                ]
            ]
        ];
        $response = $mj->post(Resources::$Email, ['body' => $body]);
        $response->success();












    }


}