<?php declare(strict_types=1);

namespace willitscale\Streetlamp\Models;

class Controller extends Context
{
    /**
     * @param string $class
     * @param string $namespace
     * @param string|null $path
     * @param string|null $accepts
     * @param bool $isController
     * @param array $preFlight
     * @param array $postFlight
     */
    public function __construct(
        string $class,
        private string $namespace,
        string|null $path = '',
        string|null $accepts = null,
        private bool $isController = false,
        array $preFlight = [],
        array $postFlight = []
    ) {
        parent::__construct($class, $path, $accepts, $preFlight, $postFlight);
    }

    /**
     * @return string
     */
    public function getNamespace(): string
    {
        return $this->namespace;
    }

    /**
     * @param string $namespace
     */
    public function setNamespace(string $namespace): void
    {
        $this->namespace = $namespace;
    }

    /**
     * @return bool
     */
    public function isController(): bool
    {
        return $this->isController;
    }

    /**
     * @param bool $isController
     */
    public function setIsController(bool $isController): void
    {
        $this->isController = $isController;
    }
}
