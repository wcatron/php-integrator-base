<?php

namespace PhpIntegrator;

use ReflectionClass;

/**
 * Fetches information about constants.
 */
class ConstantInfoFetcher implements InfoFetcherInterface
{
    use FetcherInfoTrait;

    /**
     * Retrieves information about the class that contains the specified constant.
     *
     * @param string          $name
     * @param ReflectionClass $class
     *
     * @return array
     */
    protected function getDeclaringClass($name, ReflectionClass $class)
    {
        $parent = $class;
        $declaringClass = $class;

        while ($parent = $parent->getParentClass()) {
            if (!$parent->hasConstant($name)) {
                break;
            }

            $declaringClass = $parent;
        }

        return [
            'name'            => $declaringClass->name,
            'filename'        => $declaringClass->getFileName(),
            'startLine'       => $declaringClass->getStartLine(),
            'startLineMember' => $this->getStartLineIn($declaringClass->getFileName(), $name)
        ];
    }

    /**
     * Retrieves information about the structure (class, trait, interface, ...) that contains the specified constant.
     *
     * @param string               $name
     * @param ReflectionClass|null $class
     *
     * @return array
     */
    protected function getDeclaringStructure($name, ReflectionClass $class = null)
    {
        $parent = $class;
        $declaringStructure = $class;

        while ($parent = $parent->getParentClass()) {
            if (!$parent->hasConstant($name)) {
                break;
            }

            $declaringStructure = $parent;
        }

        foreach ($declaringStructure->getInterfaces() as $interface) {
            if ($interface->hasConstant($name)) {
                $declaringStructure = $interface;
                break;
            }
        }

        return [
            'name'            => $declaringStructure->name,
            'filename'        => $declaringStructure->getFileName(),
            'startLine'       => $declaringStructure->getStartLine(),
            'startLineMember' => $this->getStartLineIn($declaringStructure->getFileName(), $name)
        ];
    }

    /**
     * Retrieves the line the specified constant starts at.
     *
     * @param string $filename
     * @param string $name
     *
     * @return int|null
     */
    protected function getStartLineIn($filename, $name)
    {
        if (!$filename) {
            return null;
        }

        $parser = new FileParser($filename);

        return $parser->getLineForRegex("/const\\s+$name/");
    }

    /**
     * {@inheritDoc}
     */
    public function createDefaultInfo(array $options)
    {
        throw new \LogicException("Not implemented yet!");
    }

    /**
     * Retrieves a data structure containing information about the specified constant.
     *
     * @param string               $name
     * @param ReflectionClass|null $class
     *
     * @return array
     */
    public function getInfo($name, ReflectionClass $class = null)
    {
        if (!is_string($name)) {
            throw new \InvalidArgumentException("The passed argument is not of the correct type!");
        }

        return [
            'name'               => $name,
            'isMethod'           => false,
            'isProperty'         => false,
            'isPublic'           => true,
            'isProtected'        => false,
            'isPrivate'          => false,
            'isStatic'           => true,

            'declaringClass'     => $class ? $this->getDeclaringClass($name, $class) : [
                'name'     => null,
                'filename' => null
            ],

            'declaringStructure' => $class ? $this->getDeclaringStructure($name, $class) : [
                'name'     => null,
                'filename' => null
            ],

            // TODO: It is not possible to directly fetch the docblock of the constant through reflection, manual
            // file parsing is required.
            'deprecated'   => false,

            'return'       => [
                'type'        => null,
                'description' => null
            ],

            'descriptions' => [
                'short'       => null,
                'long'        => null,
            ]
        ];
    }
}
