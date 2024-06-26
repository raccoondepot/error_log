<?php

declare(strict_types=1);

namespace RD\ErrorLog\Backend\Controller;

use Parsedown;
use Psr\Http\Message\ServerRequestInterface;
use RD\ErrorLog\Domain\Repository\SettingsRepository;
use Symfony\Component\HttpClient\HttpClient;
use Exception;
use TYPO3\CMS\Core\Http\JsonResponse;

class AIController
{
    protected SettingsRepository $settingsRepository;

    public function __construct(SettingsRepository $settingsRepository)
    {
        $this->settingsRepository = $settingsRepository;
    }

    public function askAction(ServerRequestInterface $request): JsonResponse
    {
        $requestBody = json_decode($request->getBody()->getContents(), true);
        $message = $requestBody['message'] ?? null;
        if ($message === null) {
            return new JsonResponse(['error' => 'No message text provided'], 400);
        }

        [$enabled, $token, $model, $prePrompt] = $this->getSettings();
        if (!$enabled || !$token || !$model) {
            return new JsonResponse(['error' => 'AI is not configured!'], 400);
        }

        $response = $this->getAIResponse($token, $model, $message, $prePrompt);
        $data = json_decode($response->getBody()->getContents());
        $parseDown = new Parsedown();
        $text =  $parseDown->text($data->choices[0]->message->content);

        return new JsonResponse(['message' => $text], 200);
    }

    public function testAction(ServerRequestInterface $request): JsonResponse
    {
        $requestBody = json_decode($request->getBody()->getContents(), true);
        $message = $requestBody['message'] ?? null;
        $token = $requestBody['token'] ?? null;
        $model = $requestBody['model'] ?? null;
        $prePrompt = $requestBody['prePrompt'] ?? null;

        if ($message === null) {
            return new JsonResponse(['error' => 'No message text provided'], 400);
        }

        if (!$token || !$model || !$prePrompt) {
            return new JsonResponse(['error' => 'AI is not configured!'], 400);
        }

        return $this->getAIResponse($token, $model, $message, $prePrompt);
    }

    private function getAIResponse(string $token, string $model, string $message, string $prePrompt): JsonResponse
    {
        $client = HttpClient::create([
            'base_uri' => 'https://api.openai.com',
        ]);
        try {
            $response = $client->request('POST', '/v1/chat/completions', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json; charset=utf-8',
                ],
                'json' => [
                    'model' => $model,
                    'messages' => [
                        ['role' => 'system', 'content' => $prePrompt],
                        ['role' => 'user', 'content' => $message]
                    ]
                ],
            ]);

            $data = json_decode($response->getContent(), true);

            return new JsonResponse($data);
        } catch (Exception $e) {
            return new JsonResponse(['error' => 'Request failed: ' . $e->getMessage()], $response->getStatusCode());
        }
    }

    private function getSettings(): array
    {
        $settings = $this->settingsRepository->getSettings();
        return [$settings->getOpenaiEnable(), $settings->getOpenaiAuthToken(), $settings->getOpenaiModel(), $settings->getPrePrompt()];
    }
}
