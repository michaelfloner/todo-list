<?php

declare(strict_types=1);

namespace App\Exception;

use Symfony\Component\HttpFoundation\Response;

class ResponseException extends \Exception
{
    public const DEFAULT_HTTP_CODE = Response::HTTP_BAD_REQUEST;

    public function __construct(
        ?string $transCode = null,
        ?int $code = self::DEFAULT_HTTP_CODE,
    ) {
        if (null === $transCode) {
            $className = strrchr(static::class, "\\");

            if ($className === false) {
                $className = '\\';
            }

            $classNames = substr($className, 1);
            $transCode = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $classNames) ?? '');
        }

        parent::__construct($transCode, $code ?? self::DEFAULT_HTTP_CODE);
    }
}
