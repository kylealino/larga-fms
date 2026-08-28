<?php

namespace App\Controllers;

class TestEmail extends BaseController
{
    public function index()
    {
        $email = \Config\Services::email();

        // IMPORTANT
        $email->setFrom(
            'kylealino@gmail.com',
            'MJESHTER FITNESS GYM'
        );

        $email->setTo('kylealino@gmail.com');

        $email->setSubject('CI4 Gmail Test');

        $email->setMessage('Email sending is working.');

        if ($email->send()) {

            echo 'EMAIL SENT SUCCESSFULLY';

        } else {

            echo $email->printDebugger(['headers']);
        }
    }
}