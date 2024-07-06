<?php

declare(strict_types=1);

namespace RD\ErrorLog\Service;

use Symfony\Component\HttpClient\HttpClient;
use Exception;
use TYPO3\CMS\Core\SingletonInterface;

class SlackService implements SingletonInterface
{
    public function sendBlocks(array $message, string $token, string $channelName)
    {
        try {
            $client = HttpClient::create(
                [
                'base_uri' => 'https://slack.com',
                ]
            );

            $client->request(
                'POST',
                "/api/chat.postMessage",
                [
                'headers' => [
                    'Accept' => 'application/json; charset=utf-8',
                ],
                'auth_bearer' => $token,
                'json' => [
                    'channel' => $channelName,
                    'blocks' => json_encode($message),
                ]
                ]
            );
        } catch (Exception $e) {
            throw new Exception('Error occurred while sending message to slack: ' . $e->getMessage());
        }
    }

    public function sendMessage(string $message, string $token, string $channelName)
    {
        try {
            $client = HttpClient::create(
                [
                'base_uri' => 'https://slack.com',
                ]
            );

            $response = $client->request(
                'POST',
                "/api/chat.postMessage",
                [
                'headers' => [
                    'Accept' => 'application/json; charset=utf-8',
                ],
                'auth_bearer' => $token,
                'json' => [
                    'channel' => $channelName,
                    'text' => $message,
                ],
                ]
            );

            return $response;
        } catch (Exception $e) {
            throw new Exception('Error occurred while sending message to slack: ' . $e->getMessage());
        }
    }
}
