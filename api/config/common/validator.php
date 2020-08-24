<?php

use Psr\Container\ContainerInterface;
use Symfony\Component\Validator\ValidatorBuilder;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

return [
    ValidatorInterface::class => static function (ContainerInterface $container) {

        AnnotationRegistry::registerLoader('class_exists');

        /** @var TranslatorInterface $translator */
        $translator = $container->get(TranslatorInterface::class);

        return (new ValidatorBuilder())
            ->enableAnnotationMapping()
            ->setTranslator($translator)
            ->setTranslationDomain('validators')
            ->getValidator();
    }
];