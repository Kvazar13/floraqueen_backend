<?php

require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
$channel = $connection->channel();

$obj = [
        'email' => 'pavel_kozheykin@outlook.com',
        'address' => 'pavel_kozheykin@outlook.com',
        'subject' => 'Test mail',
        'message' => 'Test message',
];
$channel->queue_declare('hello', false, false, false, false);

$msg = new AMQPMessage(serialize($obj));
$channel->basic_publish($msg, '', 'hello');

echo " [x] Sent 'Hello World!'\n";

$channel->close();
$connection->close();
