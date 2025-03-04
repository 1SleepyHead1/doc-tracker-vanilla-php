<?php
require_once '../../vendor/autoload.php';
require_once "../../script/globals.php";

use Twilio\Rest\Client;

try {
    $sid    = "";
    $token  = "";
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
