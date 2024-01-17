<?php

namespace App\Tests\Integration\Auth;

use App\DTO\LoginRequestDTO;
use App\DTO\RegisterRequestDTO;
use App\Tests\Integration\BaseIntegrationTestCase;
use Faker\Factory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RegisterScenarioTest extends BaseIntegrationTestCase
{
    /**
     * @covers \App\Controller\AuthController::register
     */
    public function testRegisterUser(): array
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

    /**
     * @covers \App\Controller\AuthController::login
     * @depends testRegisterUser
     */
    public function testLogin(array $credentials): void
    {
        [$email, $password] = $credentials;

        $this->validatePostEndpoint(
            '/api/v1/auth/login',
            'post',
            new LoginRequestDTO(
                $email,
                $password
            )
        );
    }
}
