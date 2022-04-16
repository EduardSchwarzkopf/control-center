<?php

namespace App\Services;

use App\Services\JWTService;
use GuzzleHttp\Client as GuzzleHttpClient;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;
use Psr\Http\Message\ResponseInterface;

class ClientApiRequest
{

    private int $expiresAfterSeconds = 1;
    private ?JWTService $jwtService = null;

    public function __construct()
    {
        $this->jwtService = new JWTService();
        $this->jwtService->expiresAfter($this->expiresAfterSeconds);
    }

    private function CreateAccessToken(): string
    {
        $this->jwtService->expiresAfter();
        return $this->jwtService->createToken();
    }

    public function get(string $url, array $parameter): ?ResponseInterface
    {
        return $this->request('GET', $url, ['query' => $parameter]);
    }

    public function post(string $url, array $form_params): ?ResponseInterface
    {
        return  $this->request('POST', $url, ['form_params' => $form_params]);
    }

    public function delete($url): ?ResponseInterface
    {
        return $this->request('DELETE', $url);
    }

    private function request(string $method, string $url, array $payload = []): ?ResponseInterface
    {
        $token = $this->CreateAccessToken();
        $request = new GuzzleHttpClient();
        $res = null;

        $options = ['headers' => ['Authorization' => 'Bearer ' . $token]] + $payload;

        try {
            $res = $request->request($method, $url, $options);
        } catch (GuzzleException $e) {
            $message = $e->getMessage();
            $this->logging("CLIENT REQUEST ERROR: $message");
        }

        return $res;
    }

    private function logging($message): void
    {
        $now = date("Y-m-d H:i:s");
        Log::warning($now . ' - CLIENT_API: ' . $message);
    }
}
