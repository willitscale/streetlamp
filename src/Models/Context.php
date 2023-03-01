<?php

declare(strict_types=1);

namespace willitscale\Streetlamp\Models;

use willitscale\Streetlamp\Enums\MediaType;
use willitscale\Streetlamp\Flight;

abstract class Context
{
    public function __construct(
        protected string $class,
        protected string|null $path = null,
        protected string|null $accepts = null,
        protected array $preFlight = [],
        protected array $postFlight = []
    ) {
    }

    /**
     * @return string
     */
    public function getClass(): string
    {
        return $this->class;
    }

    /**
     * @param string $class
     */
    public function setClass(string $class): void
    {
        $this->class = $class;
    }

    /**
     * @return string|null
     */
    public function getPath(): ?string
    {
        return $this->path;
    }

    /**
     * @param string $path
     */
    public function setPath(string $path): void
    {
        $this->path = str_replace('//', '/', $path);
        $this->path = preg_replace(
            '/{([a-z0-9_]+)}/i',
            '(?<\1>[^/]+)',
            $this->path
        );
    }

    /**
     * @param string $path
     * @return void
     */
    public function appendPath(string $path): void
    {
        $this->setPath($this->path . $path);
    }

    /**
     * @return string|null
     */
    public function getAccepts(): ?string
    {
        return $this->accepts;
    }

    /**
     * @param string|MediaType $accepts
     */
    public function setAccepts(string|MediaType $accepts): void
    {
        $this->accepts = ($accepts instanceof MediaType) ? $accepts->value : $accepts;
    }

    /**
     * @return array
     */
    public function getPreFlight(): array
    {
        return $this->preFlight;
    }

    /**
     * @param array $preFlight
     */
    public function setPreFlight(array $preFlight): void
    {
        $this->preFlight = $preFlight;
    }

    /**
     * @param string $flight
     */
    public function addPreFlight(string $flight): void
    {
        if (!in_array($flight, $this->preFlight)) {
            $this->preFlight [] = $flight;
        }
    }

    /**
     * @return array
     */
    public function getPostFlight(): array
    {
        return $this->postFlight;
    }

    /**
     * @param array $postFlight
     */
    public function setPostFlight(array $postFlight): void
    {
        $this->postFlight = $postFlight;
    }

    /**
     * @param string $flight
     */
    public function addPostFlight(string $flight): void
    {
        if (!in_array($flight, $this->postFlight)) {
            $this->postFlight [] = $flight;
        }
    }
}
