<?php

namespace App\Classe;


use Mailjet\Client;
use Mailjet\Resources;

class Mail 
{
    public function send($to_email, $to_name, $subject, $template, $vars = null)
    {
        //recuperation du template dans mail
        $content = file_get_contents(dirname(__DIR__).'/Mail/welcome.html');

        // recuperation des variables facultatives

        if($vars){
            foreach($vars as $key=>$var) {
            $content = str_replace("{'.$key.'}", $var, $content);
            }
        }

        $mj = new Client($_ENV['MJ_APIKEY_PUBLIC'], $_ENV['MJ_APIKEY_PUBLIC'],true ,['version' => 'v3.1']);
        $body = [
            'Messages' => [
                [
                    'From' => [
                        'Email' => "rrighistmg@gmail.com",
                        'Name' => "La Boutique Française"
                    ],
                    'To' => [
                        [
                            'Email' => $to_email,
                            'Name' => $to_name
                        ]
                    ],
                    'TemplateID' => 5942603,
                    'TemplateLanguage' => true,
                    'Subject' =>  $subject,
                    'Variables' => [
                        'content' => $content
                    ]
                ]
            ]
        ];
        
        $mj->post(Resources::$Email, ['body' => $body]);

    }
}
