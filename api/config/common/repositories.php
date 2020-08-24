<?php

return [
    \App\Auth\Entity\User\Repository\IUserRepository::class => Di\get(\App\Auth\Entity\User\Repository\UserRepository::class),
];