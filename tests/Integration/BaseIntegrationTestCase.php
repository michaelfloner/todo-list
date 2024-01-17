<?php

namespace App\Tests\Integration;


use App\DTO\LoginRequestDTO;
use App\DTO\RegisterRequestDTO;
use Faker\Factory;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Serializer;

abstract class BaseIntegrationTestCase extends WebTestCase
{
    protected ?Serializer $serializer = null;

    protected static ?KernelBrowser $client = null;

    protected function setUp(): void
    {
        parent::setUp();

        if (self::$client === null) {
            self::$client = static::createClient([
                'environment' => $_SERVER['_KERNEL_ENV'] ?? $_SERVER['APP_ENV'],
            ]);
        }
    }

    public function tearDown(): void
    {
        self::$kernel?->getContainer()->get('doctrine')->getConnection()->close();
    }

    public static function decodeResponseToArray(Response $response): ?array
    {
        return json_decode((string) $response->getContent(), true);
    }

    protected function createUser(): array
    {
        $faker = Factory::create();
        $password = $faker->password(12);
        $email = $faker->email();

        $response = $this->validatePostEndpoint(
            '/api/v1/auth/register',
            Request::METHOD_POST,
            new RegisterRequestDTO($email, $password),
            Response::HTTP_CREATED
        );

        $responseBody = self::decodeResponseToArray($response);

        $this->assertArrayHasKey('email', $responseBody);
        $this->assertSame($email, $responseBody['email']);

        return [$email, $password];
    }

    protected function loginUser(array $credentials): string
    {
        [$email, $password] = $credentials;

        $response = $this->validatePostEndpoint(
            '/api/v1/auth/login',
            'post',
            new LoginRequestDTO(
                $email,
                $password
            )
        );

        $responseBody = self::decodeResponseToArray($response);

        return $responseBody['token'];
    }

    protected function validateGetEndpoint(
        string $endpoint,
        string $method = 'get',
        ?array $urlParams = null,
        ?int $expectedStatusCode = Response::HTTP_OK,
        ?string $accessToken = null,
        array $headers = []
    ): Response
    {
        $url = $endpoint;

        if (null !== $urlParams) {
            $rawParams = array_filter($urlParams, static fn($key) => !str_contains($key, '{'), ARRAY_FILTER_USE_KEY);
            $hashParams = array_filter($urlParams, static fn($key) => str_contains($key, '{'), ARRAY_FILTER_USE_KEY);
            $url = str_replace(array_keys($hashParams), array_values($hashParams), $url);

            if (count($rawParams) > 0) {
                $url .= '?' . http_build_query($rawParams);
            }
        }

        $requestHeaders = array_merge([
            'CONTENT_TYPE' => 'application/json',
            'HTTP_AUTHORIZATION' => $accessToken ? "Bearer {$accessToken}" : 'None',
        ], $headers);

        self::$client->request($method, $url, [], [], $requestHeaders);

        $response = self::$client->getResponse();

        $this->assertSame(
            $expectedStatusCode,
            $response->getStatusCode(),
            "Unexpected status code {$response->getStatusCode()} received, expected: {$expectedStatusCode}"
        );

        return $response;
    }

    protected function validatePostEndpoint(
        string $endpoint,
        string $method,
               $requestBody = null,
        ?int $expectedStatusCode = Response::HTTP_OK,
        ?array $urlParams = null,
        ?string $accessToken = null,
        ?array $files = []
    ): Response
    {
        $url = $endpoint;

        if (null !== $urlParams) {
            $url = str_replace(array_keys($urlParams), array_values($urlParams), $url);
        }

        self::$client->request($method, $url, [], $files, [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_AUTHORIZATION' => $accessToken ? "Bearer {$accessToken}" : 'None',
        ], $requestBody ? $this->serializeDTO($requestBody) : '{}');
        $response = self::$client->getResponse();

        $this->assertSame(
            $expectedStatusCode,
            $response->getStatusCode(),
            "[{$method} {$url}] Unexpected status code {$response->getStatusCode()} received, expected: {$expectedStatusCode}\n\nResponse:\n{$response->getContent()}"
        );

        return $response;
    }

    private function serializeDTO($dto): string
    {
        if (null === $this->serializer) {
            if (null === self::getContainer()) {
                self::bootKernel();
            }
            $this->serializer = self::getContainer()->get('serializer');
        }

        return $this->serializer->serialize($dto, 'json');
    }
}
