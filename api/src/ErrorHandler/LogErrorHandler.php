<?php

namespace App\ErrorHandler;

use Psr\Log\LoggerInterface;
use Slim\Handlers\ErrorHandler;
use Psr\Http\Message\ResponseFactoryInterface;
use Slim\Interfaces\CallableResolverInterface;

class LogErrorHandler extends ErrorHandler
{
    public function __construct(CallableResolverInterface $callableResolver,
                                ResponseFactoryInterface $responseFactory,
                                LoggerInterface $logger)
    {
        parent::__construct($callableResolver, $responseFactory);

        $this->logger = $logger;
    }

    protected function writeToErrorLog(): void
    {
        $this->logger->error($this->exception->getMessage(), [
            'exception' => $this->exception,
            'url' => $this->request->getUri(),
        ]);
    }
}