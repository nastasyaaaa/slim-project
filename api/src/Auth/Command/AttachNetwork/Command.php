<?php


namespace App\Auth\Command\AttachNetwork;


class Command
{
    public int $id;
    public string $network;
    public string $identity;

    public function __construct(int $id, string $network, string $identity)
    {
        $this->id = $id;
        $this->network = $network;
        $this->identity = $identity;
    }
}