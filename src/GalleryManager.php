<?php

namespace Chuoke\UnifyGallery;

use Chuoke\UnifyGallery\Bing\BingAdapter;
use Chuoke\UnifyGallery\Pexels\PexelsAdapter;
use Chuoke\UnifyGallery\Pixabay\PixabayAdapter;
use Chuoke\UnifyGallery\Unsplash\UnsplashAdapter;
use InvalidArgumentException;

class GalleryManager
{
    protected array $galleries = [];

    protected array $customCreators = [];

    protected Config|null $config;

    public function __construct(array $config)
    {
        $this->config = new Config($config);
    }

    /**
     * Get the default driver name.
     *
     * @return string|null
     */
    public function getDefaultGallery()
    {
        return $this->config->get('default') ?? null;
    }

    /**
     * Get a Gallery instance.
     *
     * @throws \InvalidArgumentException
     */
    public function gallery(string $name = null): Gallery
    {
        return $this->driver($name);
    }

    /**
     * Get a driver instance.
     *
     * @throws \InvalidArgumentException
     */
    public function driver(string $driver = null): Gallery
    {
        $driver = $driver ?: $this->getDefaultGallery();

        if (is_null($driver)) {
            throw new InvalidArgumentException(sprintf(
                'Unable to resolve NULL driver for [%s].',
                static::class
            ));
        }

        return $this->drivers[$driver] ?? ($this->drivers[$driver] = $this->resolve($driver));
    }

    /**
     * Resolve the given driver.
     *
     * @throws \InvalidArgumentException
     */
    protected function resolve(string $name): Gallery
    {
        $config = $this->getDriverConfig($name);

        if (isset($this->customCreators[$name])) {
            return $this->callCustomCreator($name, $config);
        }

        $driverMethod = 'create' . ucfirst($name) . 'Driver';

        if (! method_exists($this, $driverMethod)) {
            throw new InvalidArgumentException("Driver [{$name}] is not supported.");
        }

        $driver = $this->{$driverMethod}($config);

        return $this->makeGallery($driver, $config);
    }

    protected function callCustomCreator(string $driver, array $config): GalleryAdapter
    {
        $driver = $this->customCreators[$driver]($config);

        return $driver;
    }

    public function createBingDriver(array $config): BingAdapter
    {
        return new BingAdapter();
    }

    public function createPexelsDriver(array $config): PexelsAdapter
    {
        return new PexelsAdapter($config['api_key']);
    }

    public function createUnsplashDriver(array $config): UnsplashAdapter
    {
        return new UnsplashAdapter($config['api_key']);
    }

    public function createPixabayDriver(array $config): PixabayAdapter
    {
        return new PixabayAdapter($config['api_key']);
    }

    public function makeGallery(GalleryAdapter $adapter, array $config = []): Gallery
    {
        return new Gallery($adapter, $config);
    }

    protected function getDriverConfig(string $name): array
    {
        $drivers = $this->config->get('drivers') ?? [];

        return $drivers[$name] ?? $this->config->all();
    }

    /**
     * Dynamically call the default driver instance.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return $this->gallery()->$method(...$parameters);
    }
}
