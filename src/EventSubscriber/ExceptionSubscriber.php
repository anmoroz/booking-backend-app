<?php

declare(strict_types=1);


namespace App\EventSubscriber;

use App\Exception\ValidationException;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\KernelInterface;

class ExceptionSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private KernelInterface $kernel,
        private LoggerInterface $logger
    )
    {

    }

    /**
     * @param ExceptionEvent $event
     */
    public function onExceptionEvent(ExceptionEvent $event)
    {
        $throwable = $event->getThrowable();

        $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR;

        if ($throwable instanceof HttpExceptionInterface) {
            $statusCode = $throwable->getStatusCode();
        }

        $message = Response::$statusTexts[$statusCode];

        if (
            $statusCode !== Response::HTTP_INTERNAL_SERVER_ERROR
            //&& $statusCode !== Response::HTTP_NOT_FOUND
            && $throwable->getMessage() !== ''
        ) {
            $message = $throwable->getMessage();
        }

        $data = [
            'status' => 'error',
            'code' => $statusCode,
            'message' => $message
        ];

        if (
            $statusCode === Response::HTTP_INTERNAL_SERVER_ERROR
            && $this->kernel->getEnvironment() === 'dev'
        ) {
            $data['trace'] = $throwable->getTraceAsString();
        }

        if ($throwable instanceof ValidationException) {
            $data['message'] = $throwable->getValidationErrors();
        }

        $event->setResponse(new JsonResponse($data, $statusCode));

        if ($statusCode === Response::HTTP_INTERNAL_SERVER_ERROR) {
            $this->logger->error($throwable->getMessage(), $throwable->getTrace());
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            ExceptionEvent::class => 'onExceptionEvent',
        ];
    }
}