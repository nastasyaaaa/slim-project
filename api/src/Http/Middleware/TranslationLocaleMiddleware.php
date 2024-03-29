<?php

namespace App\Http\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Symfony\Component\Translation\Translator;

class TranslationLocaleMiddleware implements MiddlewareInterface
{
    private Translator $translator;

    /**
     * TranslationLocaleMiddleware constructor.
     * @param Translator $translator
     */
    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $locale = $request->getHeaderLine('Accept-Language');

        if (!empty($locale)) {
            $this->translator->setLocale($locale);
        }

        return $handler->handle($request);
    }
}