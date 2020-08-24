<?php

namespace App\Http\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\UploadedFileInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ServerRequestInterface;

class ClearEmptyInputMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $request = $request
            ->withParsedBody(self::filterStrings($request->getParsedBody()))
            ->withUploadedFiles(self::filterFiles($request->getUploadedFiles())
            );

        return $handler->handle($request);
    }

    private static function filterStrings($items)
    {
        if (!is_array($items)) {
            return $items;
        }

        foreach ($items as $name => $value) {
            if (is_string($value)) {
                $items[$name] = trim($value);
            } else {
                $items[$name] = self::filterStrings($value);
            }
        }

        return $items;
    }

    private static function filterFiles(array $files)
    {
        $result = [];

        /**
         * @var string $key
         * @var array|UploadedFileInterface $file
         */
        foreach ($files as $key => $file) {
            if ($file instanceof UploadedFileInterface) {
                if ($file->getError() !== UPLOAD_ERR_NO_FILE) {
                    $result[$key] = $file;
                }
            } else {
                $result[$key] = self::filterFiles($file);
            }
        }

        return $result;
    }
}