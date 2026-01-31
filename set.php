<?php
require_once __DIR__ . '/../vendor/autoload.php';
/** @var array $config */
$config = require __DIR__ . '/config.php';
try {
    // Create Telegram API object
    $telegram = new Longman\TelegramBot\Telegram($config['api_key'], $config['bot_username']);
    // Set the webhook
    $result = $telegram->setWebhook($config['webhook']['url']);

    // To use a self-signed certificate, use this line instead
    // $result = $telegram->setWebhook($config['webhook']['url'], ['certificate' => $config['webhook']['certificate']]);

    echo $result->getDescription();
} catch (Longman\TelegramBot\Exception\TelegramException $e) {
    echo $e->getMessage();
}
