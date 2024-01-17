<?php

namespace App\Tests\Integration\Task;

use App\DTO\TaskCreateRequestDTO;
use App\Entity\TaskState;
use App\Tests\Integration\BaseIntegrationTestCase;
use Faker\Factory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TaskScenarioTest extends BaseIntegrationTestCase
{
    /**
     * @covers \App\Controller\TaskController::createTask
     */
    public function testTask(): array
    {
        $credentials = $this->createUser();
        $token = $this->loginUser($credentials);
        $faker = Factory::create();

        $response = $this->validatePostEndpoint(
            '/api/v1/task',
            Request::METHOD_POST,
            new TaskCreateRequestDTO(
                name: $faker->name,
                description: $faker->text(100)
            ),
            Response::HTTP_CREATED,
            accessToken: $token
        );

        $responseBody = self::decodeResponseToArray($response);

        $this->assertArrayHasKey('id', $responseBody);

        return [$responseBody['id'], $token];
    }

    /**
     * @covers \App\Controller\TaskController::getTask
     * @depends testTask
     */
    public function testGetTask(array $data): void
    {
        [$id, $token] = $data;

        $response = $this->validateGetEndpoint(
            '/api/v1/task/{id}',
            Request::METHOD_GET,
            urlParams: [
                '{id}' => $id,
            ],
            accessToken: $token
        );

        $responseBody = self::decodeResponseToArray($response);
        $this->assertArrayHasKey('id', $responseBody);
        $this->assertArrayHasKey('name', $responseBody);
        $this->assertArrayHasKey('state', $responseBody);
    }

    /**
     * @covers \App\Controller\TaskController::taskToInProgress
     * @depends testTask
     */
    public function testToInProgress(array $data): void
    {
        [$id, $token] = $data;

        $response = $this->validateGetEndpoint(
            '/api/v1/task/{id}/in-progress',
            Request::METHOD_PATCH,
            urlParams: [
                '{id}' => $id,
            ],
            accessToken: $token
        );

        $responseBody = self::decodeResponseToArray($response);
        $this->assertSame(TaskState::IN_PROGRESS->value, $responseBody['state']);
    }

    /**
     * @covers \App\Controller\TaskController::taskCompleted
     * @depends testTask
     */
    public function testCompleteTask(array $data): void
    {
        [$id, $token] = $data;

        $response = $this->validateGetEndpoint(
            '/api/v1/task/{id}/completed',
            Request::METHOD_PATCH,
            urlParams: [
                '{id}' => $id,
            ],
            accessToken: $token
        );

        $responseBody = self::decodeResponseToArray($response);
        $this->assertSame(TaskState::COMPLETED->value, $responseBody['state']);
    }
}
