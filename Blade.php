<?php

namespace Larso\View;

use Illuminate\Config\Repository;
use Illuminate\Events\Dispatcher;
use Illuminate\Container\Container;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Contracts\Container\Container as ContainerContract;
use Illuminate\Contracts\View\Factory;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Facade;
use Illuminate\View\Compilers\BladeCompiler;
use Illuminate\View\ViewServiceProvider;
use Illuminate\Support\Facades\View as ViewFacade;

class Blade
{
    /**
     * @var Factory
     */
    protected $factory;

    /**
     * @var BladeCompiler
     */
    protected $compiler;

    /**
     * @var string
     */
    protected $viewPaths;

    /**
     * @var string
     */
    protected $cachePath;

    /**
     * @var Container
     */
    protected $container;

    /**
     * Get a new Blade
     *
     * @param string $viewPaths
     * @param string $cachePath
     * @param Container|null $container
     * @return \Illuminate\Contracts\View\Factory
     */
    public function __construct(string $viewPaths, string $cachePath, ?Container $container = null)
    {
        $this->viewPaths = Arr::wrap($viewPaths);
        $this->cachePath = $cachePath;

        $this->init();

        /**
         * Set App Facade
         */
        $app = $this->container;
        ViewFacade::setFacadeApplication($app);
    }

    public function init()
    {
        $this->container = new Container();

        $this->container->singleton('files', function () {
            return new Filesystem();
        });

        $this->container->singleton('events', function () {
            return new Dispatcher();
        });

        $this->container->singleton('config', function () {
            return new Repository([
                'view.paths'    => $this->viewPaths,
                'view.compiled' => $this->cachePath,
            ]);
        });

        (new ViewServiceProvider($this->container))->register();

        $this->factory = $this->container->get('view');
        $this->compiler = $this->container->get('blade.compiler');
    }

    public function __call(string $name, array $arguments)
    {
        if (method_exists($this->compiler, $name)) {
            return $this->compiler->{$name}(...$arguments);
        }

        return $this->factory->{$name}(...$arguments);
    }

    /**
     * Get a new Blade
     *
     * @param string $viewPath
     * @param string $cachePath
     * @param Container|null $container
     * @return \Illuminate\Contracts\View\Factory
     */
    public static function new(string $viewPath, string $cachePath, ?Container $container = null)
    {
        return new static($viewPath, $cachePath, $container);
    }
}
