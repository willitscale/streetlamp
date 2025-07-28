<?php

namespace willitscale\Streetlamp\Traits;

use ReflectionParameter;
use willitscale\Streetlamp\Attributes\DataBindings\ArrayMapInterface;
use willitscale\Streetlamp\Attributes\Parameter\Parameter;
use willitscale\Streetlamp\Attributes\Validators\ValidatorInterface;
use willitscale\Streetlamp\Exceptions\MethodParameterNotMappedException;
use willitscale\Streetlamp\Models\Route;

trait BuildMethodParameters
{
    private function buildMethodParameters(
        Route $route,
        ReflectionParameter $parameter
    ): void {
        $attributes = $parameter->getAttributes();

        if (empty($attributes)) {
            throw new MethodParameterNotMappedException("No attributes against method parameter");
        }

        $validators = [];
        $parameterInstance = null;
        $arrayMapInterface = null;

        foreach ($attributes as $attribute) {
            $instance = $attribute->newInstance();
            if ($instance instanceof Parameter) {
                $instance->setType($parameter->getType()->getName());
                $parameterInstance = $instance;
            } elseif ($instance instanceof ValidatorInterface) {
                $validators [] = $instance;
            } elseif ($instance instanceof ArrayMapInterface) {
                $arrayMapInterface = $instance;
            }
        }

        foreach ($validators as $validator) {
            $parameterInstance->addValidator($validator);
        }

        if (empty($parameterInstance)) {
            throw new MethodParameterNotMappedException("No valid Parameter attribute against method parameter");
        }

        if (!empty($arrayMapInterface)) {
            $parameterInstance->setArrayMap($arrayMapInterface);
        }

        $route->addParameter(
            $parameter->getName(),
            $parameterInstance
        );
    }
}