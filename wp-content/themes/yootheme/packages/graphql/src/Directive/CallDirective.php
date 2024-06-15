<?php

namespace YOOtheme\GraphQL\Directive;

use YOOtheme\Container;
use YOOtheme\GraphQL\Type\Definition\Directive;
use YOOtheme\GraphQL\Type\Definition\Type;
use YOOtheme\GraphQL\Utils\Middleware;

class CallDirective extends Directive
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * Constructor.
     *
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        parent::__construct([
            'name' => 'call',
            'args' => [
                [
                    'name' => 'func',
                    'type' => Type::string(),
                ],
                [
                    'name' => 'args',
                    'type' => Type::string(),
                ],
            ],
            'locations' => ['ENUM_VALUE', 'FIELD_DEFINITION'],
        ]);

        $this->container = $container;
    }

    /**
     * Resolve value from function callback.
     *
     * @param array      $params
     * @param Middleware $resolver
     *
     * @return \Closure|void
     */
    public function __invoke(array $params, Middleware $resolver)
    {
        // override default resolver
        $resolver->setHandler($this->container->callback($params['func']));

        // merge additional arguments
        if (isset($params['args']) && is_array($arguments = json_decode($params['args'], true))) {
            return fn($value, $args, $context, $info, $next) => $next(
                $value,
                $args + $arguments,
                $context,
                $info,
            );
        }
    }
}
