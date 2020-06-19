<?php


namespace App\Http;


use Psr\Http\Message\ResponseInterface;

class Http
{
    /**
     * @param ResponseInterface $response
     * @param $data
     * @return ResponseInterface
     * @throws \JsonException
     */
    public static function json(ResponseInterface $response, $data)
    {
        $response->getBody()->write(json_encode($data, JSON_THROW_ON_ERROR, 512));
        return $response->withHeader('Content-Type', 'application/json');
    }

}