<?php

declare(strict_types=1);

namespace RD\ErrorLog\Backend\Controller;

use Exception;
use Psr\Http\Message\ServerRequestInterface;
use RD\ErrorLog\Service\SlackService;
use TYPO3\CMS\Core\Http\JsonResponse;

class SlackController
{
    private SlackService $slackService;

    public function __construct(SlackService $slackService)
    {
        $this->slackService = $slackService;
    }

    public function testAction(ServerRequestInterface $request): JsonResponse
    {
        $requestBody = json_decode($request->getBody()->getContents(), true);
        $message = $requestBody['message'] ?? null;
        $token = $requestBody['token'] ?? null;
        $channelId = $requestBody['channelId'] ?? null;

        if (!$token || !$channelId) {
            return new JsonResponse(['error' => 'Slack is not configured!'], 400);
        }

        return $this->sendMessage($token, $channelId, $message);
    }

    private function sendMessage(string $token, string $channelId, string $message): JsonResponse
    {
        try {
            $response = $this->slackService->sendMessage($message, $token, $channelId);
            $data = json_decode($response->getContent(), true);
            if ($data['ok'] === true) {
                return new JsonResponse(['message' => 'Message sent to slack!']);
            }
        } catch (Exception $e) {
            return new JsonResponse(['error' => 'Error occurred while sending message to slack: ' . $e->getMessage()], 500);
        }

        return new JsonResponse(['error' => 'Error occurred while sending message to slack!'], 500);
    }
}
