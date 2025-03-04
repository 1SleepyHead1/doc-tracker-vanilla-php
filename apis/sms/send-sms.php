<?php
require_once '../../vendor/autoload.php';
require_once "../../script/globals.php";

use Twilio\Rest\Client;

try {
    $sid    = "AC32b99d0b0d51fccfa277baca1c54c646";
    $token  = "5e829381d82f1391a1c23fea90576781";
    $twilio = new Client($sid, $token);
    $message = $twilio->messages
        ->create(
            "+639289294438", // to
            array(
                "from" => "+17086160621",
                "body" => "Hello World!"
            )
        );
} catch (\Throwable $th) {
    //throw $th;
}
