<?php

namespace YOOtheme;

use YOOtheme\Theme\ViewHelperInterface;
use YOOtheme\View\HtmlElementInterface;
use YOOtheme\View\HtmlHelperInterface;

/**
 * @method string builder($node, $params = [])
 * @mixin ViewHelperInterface
 * @mixin HtmlHelperInterface
 * @mixin HtmlElementInterface
 */
class View implements \ArrayAccess
{
    /**
     * @var \SplStack
     */
    protected $loader;

    /**
     * @var array
     */
    protected $template = [];

    /**
     * @var array
     */
    protected $parameters = [];

    /**
     * @var array
     */
    protected $filters = [];

    /**
     * @var array
     */
    protected $globals = [];

    /**
     * @var array
     */
    protected $helpers = [];

    /**
     * @var array
     */
    protected $functions = [];

    /**
     * @var array
     */
    private $evalParameters;

    /**
     * Constructor.
     *
     * @param callable $loader
     */
    public function __construct(callable $loader = null)
    {
        $this->loader = new \SplStack();
        $this->loader->push([$this, 'evaluate']);

        if ($loader) {
            $this->addLoader($loader);
        }

        $this->addFunction('e', [$this, 'escape']);
    }

    /**
     * Renders a template (shortcut).
     *
     * @param string $name
     * @param mixed  $parameters
     *
     * @return string|false
     */
    public function __invoke($name, $parameters = [])
    {
        return $this->render($name, $parameters);
    }

    /**
     * Handles dynamic calls to the class.
     *
     * @param string $name
     * @param array  $args
     *
     * @return mixed
     */
    public function __call($name, $args)
    {
        if (!isset($this->functions[($key = strtolower($name))])) {
            trigger_error(
                sprintf('Call to undefined method %s::%s()', get_class($this), $name),
                E_USER_ERROR,
            );
        }

        return call_user_func_array($this->functions[$key], $args);
    }

    /**
     * Gets the global parameters.
     *
     * @return array
     */
    public function getGlobals()
    {
        return $this->globals;
    }

    /**
     * Adds a global parameter.
     *
     * @param string $name
     * @param mixed  $value
     *
     * @return $this
     */
    public function addGlobal($name, $value)
    {
        $this->globals[$name] = $value;

        return $this;
    }

    /**
     * Adds a helper.
     *
     * @param string|callable $helper
     *
     * @return $this
     */
    public function addHelper($helper)
    {
        if (is_callable($helper)) {
            $helper($this);
        } elseif (class_exists($helper)) {
            new $helper($this);
        }

        return $this;
    }

    /**
     * Adds a custom function.
     *
     * @param string   $name
     * @param callable $callback
     *
     * @return View
     */
    public function addFunction($name, callable $callback)
    {
        $this->functions[strtolower($name)] = $callback;

        return $this;
    }

    /**
     * Adds a loader callback.
     *
     * @param callable $loader
     * @param string   $filter
     *
     * @return $this
     */
    public function addLoader(callable $loader, $filter = null)
    {
        if (is_null($filter)) {
            $next = $this->loader->top();

            $this->loader->push(function ($name, array $parameters = []) use ($loader, $next) {
                return $loader($name, $parameters, $next);
            });
        } else {
            $this->filters[$filter][] = $loader;
        }

        return $this;
    }

    /**
     * Applies multiple functions.
     *
     * @param mixed  $value
     * @param string $functions
     *
     * @return string
     */
    public function apply($value, $functions)
    {
        $functions = explode('|', strtolower($functions));

        return array_reduce(
            $functions,
            fn($value, $function) => call_user_func([$this, $function], $value),
            $value,
        );
    }

    /**
     * Converts special characters to HTML entities.
     *
     * @param string $value
     * @param string $functions
     *
     * @return string
     */
    public function escape($value, $functions = '')
    {
        $value = strval($value);

        if ($functions) {
            $value = $this->apply($value, $functions);
        }

        return htmlspecialchars($value, ENT_COMPAT, 'UTF-8');
    }

    /**
     * Renders a template.
     *
     * @param string         $name
     * @param array|callable $parameters
     *
     * @return string|callable|false
     */
    public function render($name, $parameters = [])
    {
        if (is_callable($parameters)) {
            return fn() => $this->render(
                $name,
                call_user_func_array($parameters, func_get_args()) ?: [],
            );
        }

        $next = $this->loader->top();

        foreach ($this->filters as $filter => $loaders) {
            if (!Str::is($filter, $name)) {
                continue;
            }

            foreach ($loaders as $loader) {
                $next = fn($name, array $parameters = []) => $loader($name, $parameters, $next);
            }
        }

        return $next(
            $name,
            array_replace(end($this->parameters) ?: $this->globals, $parameters, [
                '_root' => empty($this->template),
            ]),
        );
    }

    /**
     * Renders current template.
     *
     * @param mixed $parameters
     *
     * @return string|false
     */
    public function self($parameters = [])
    {
        return $this->render(end($this->template), $parameters);
    }

    /**
     * Evaluates a template.
     *
     * @param string $template
     * @param array  $parameters
     *
     * @return string|false
     */
    public function evaluate($template, array $parameters = [])
    {
        $this->template[] = $template;
        $this->parameters[] = $this->evalParameters = $parameters;

        unset($template, $parameters);
        extract($this->evalParameters, EXTR_SKIP);
        unset($this->evalParameters);

        $__file = end($this->template);
        $__dir = dirname($__file);

        if (is_file($__file)) {
            ob_start();
            require $__file;

            $result = ob_get_clean();
        }

        array_pop($this->template);
        array_pop($this->parameters);

        return $result ?? false;
    }

    /**
     * Checks if a helper is registered.
     *
     * @param string $name
     *
     * @return bool
     */
    #[\ReturnTypeWillChange]
    public function offsetExists($name)
    {
        return isset($this->helpers[$name]);
    }

    /**
     * Gets a helper.
     *
     * @param string $name
     *
     * @return mixed
     */
    #[\ReturnTypeWillChange]
    public function offsetGet($name)
    {
        if (!$this->offsetExists($name)) {
            throw new \InvalidArgumentException(sprintf('Undefined helper "%s"', $name));
        }

        return $this->helpers[$name];
    }

    /**
     * Sets a helper.
     *
     * @param string $name
     * @param object $helper
     */
    #[\ReturnTypeWillChange]
    public function offsetSet($name, $helper)
    {
        $this->helpers[$name] = $helper;
    }

    /**
     * Removes a helper.
     *
     * @param string $name
     */
    #[\ReturnTypeWillChange]
    public function offsetUnset($name)
    {
        throw new \LogicException(sprintf('You can\'t remove a helper "%s"', $name));
    }
}
