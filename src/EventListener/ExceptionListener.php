<?php

declare(strict_types=1);

namespace App\EventListener;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

#[AsEventListener]
readonly class ExceptionListener
{
    public function __construct(
        private string $environment,
    ) {
    }

    public function __invoke(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        $data = [
            'message' => $exception->getMessage(),
        ];

        $trace = $this->getTrace($exception);

        if ($trace !== null) {
            $exceptionTrace = $this->getTrace($exception);
            $data['trace'] = $exceptionTrace !== null
                ? explode('#', $exceptionTrace)
                : $exceptionTrace;
        }

        $content = json_encode($data);

        $response = new Response();
        $response->headers->add([
            'Content-Type' => 'application/json',
        ]);

        $response->setContent($content !== false ? $content : null);
        $response->setStatusCode($this->getStatusCode($exception));

        $event->setResponse($response);
    }

    private function getStatusCode(\Throwable $exception): int
    {
        if ($exception->getCode() !== 0) {
            return $exception->getCode();
        }

        if ($exception instanceof HttpExceptionInterface) {
            return $exception->getStatusCode();
        }

        return Response::HTTP_INTERNAL_SERVER_ERROR;
    }

    private function getTrace(\Throwable $exception): ?string
    {
        $devMode = $this->environment === 'dev' || $this->environment === 'test';

        if (!$devMode) {
            return null;
        }

        return sprintf(
            '%s(%d)%s',
            $exception->getFile(),
            $exception->getLine(),
            $exception->getTraceAsString(),
        );
    }
}
