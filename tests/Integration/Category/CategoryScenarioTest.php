<?php

namespace App\Tests\Integration\Category;

use App\DTO\CategoryCreateRequestDTO;
use App\Tests\Integration\BaseIntegrationTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CategoryScenarioTest extends BaseIntegrationTestCase
{
    public const CATEGORY_NAME = 'category 1';

    /**
     * @covers \App\Controller\CategoryController::createCategory
     */
    public function testCreateCategory(): array
    {
        $credentials = $this->createUser();
        $token = $this->loginUser($credentials);

        $response = $this->validatePostEndpoint(
            '/api/v1/categories',
            Request::METHOD_POST,
            new CategoryCreateRequestDTO(
                self::CATEGORY_NAME,
            ),
            Response::HTTP_CREATED,
            accessToken: $token
        );

        $responseBody = self::decodeResponseToArray($response);

        $this->assertArrayHasKey('id', $responseBody);

        return [$responseBody['id'], $token];
    }

    /**
     * @covers \App\Controller\CategoryController::createCategory
     * @depends testCreateCategory
     */
    public function testCreateCategoryWithSameName(array $data): void
    {
        [, $token] = $data;

        $this->validatePostEndpoint(
            '/api/v1/categories',
            Request::METHOD_POST,
            new CategoryCreateRequestDTO(
                self::CATEGORY_NAME
            ),
            Response::HTTP_CONFLICT,
            accessToken: $token
        );
    }

    /**
     * @covers \App\Controller\CategoryController::getCategory
     * @depends testCreateCategory
     */
    public function testGetCategory(array $data): void
    {
        [$id, $token] = $data;

        $response = $this->validateGetEndpoint(
            '/api/v1/categories/{id}',
            Request::METHOD_GET,
            urlParams: [
                '{id}' => $id,
            ],
            accessToken: $token
        );

        $responseBody = self::decodeResponseToArray($response);
        $this->assertArrayHasKey('id', $responseBody);
        $this->assertArrayHasKey('name', $responseBody);
    }
}
