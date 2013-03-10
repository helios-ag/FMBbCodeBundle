<?php

namespace FM\BbcodeBundle\Translation\Loader;


/**
 * JsonFileLoader loads Json files messages translation.
 *
 * @author Alexandre Quercia <alquerci@email.com>
 */
class JsonFileLoader extends FileLoader
{
    /**
     * Loads the resource and return the result.
     *
     * @param mixed $resource
     * @param string $type
     *
     * @return array
     */
    public function load($resource, $type = null)
    {
        $path = $this->locator->locate($resource);

        $content = $this->parseFile($path);

        return (array) $content;
    }

    /**
     * @param mixed $resource
     * @param string $type
     */
    public function supports($resource, $type = null)
    {
        return is_string($resource) && 'json' === pathinfo($resource, PATHINFO_EXTENSION);
    }

    /**
     * Parse a file.
     *
     * @param string $file
     * @return array
     */
    private function parseFile($file)
    {
        $content = json_decode(\file_get_contents($file), true);

        if (!$content) {
            return array();
        }

        return (array) $content;
    }
}
