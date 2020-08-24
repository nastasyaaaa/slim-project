<?php

use Psr\Container\ContainerInterface;
use Symfony\Component\Validator\ValidatorBuilder;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Symfony\Component\Validator\Validator\ValidatorInterface;

return [
    ValidatorInterface::class => static function (ContainerInterface $container) {

        AnnotationRegistry::registerLoader('class_exists');

        return (new ValidatorBuilder())
            ->enableAnnotationMapping()
            ->getValidator();
    }
];