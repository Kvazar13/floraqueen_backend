<?php

require_once __DIR__ . '/vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;

function send_php_mail($email, $subject, $message){
    $headers = 'From: pavel_kozheykin@outlook.com'       . "\r\n" .
        'Reply-To: pavel_kozheykin@outlook.com' . "\r\n" .
        'X-Mailer: PHP/' . phpversion();
    mail($email, $subject, $message, $headers);
}

function send_mailchimp_mail($message){
    try {
        $mailchimp = new MailchimpTransactional\ApiClient();
        $mailchimp->setApiKey('NK8bnQlkZAoouasyN_KWbg');
        $response = $mailchimp->messages->send(["message" => [$message]]);
        print_r($response);
    } catch (Error $e) {
        echo 'Error: ',  $e->getMessage(), "\n";
    }
}

$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
$channel = $connection->channel();

$channel->queue_declare('hello', false, false, false, false);

echo " [*] Waiting for messages. To exit press CTRL+C\n";

$callback = function ($msg) {
    echo ' [x] Received ', $msg->body, "\n";

    extract(unserialize($msg->body));
    send_php_mail($email, $subject, $message);
    send_mailchimp_mail($message);

};

$channel->basic_consume('hello', '', false, true, false, false, $callback);

while ($channel->is_open()) {
    $channel->wait();
}

$channel->close();
$connection->close();
