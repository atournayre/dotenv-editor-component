<?php

/*
 * This file is part of the dotenv-editor component.
 *
 * (c) Aurélien Tournayre <aurelien.tournayre@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Atournayre\Component\DotEnvEditor;

use Atournayre\Component\DotEnvEditor\Exception\DotEnvEditorAddVariableTypeException;
use Atournayre\Component\DotEnvEditor\Exception\DotEnvEditorMissingVariableException;

/**
 * @author Aurélien Tournayre <aurelien.tournayre@gmail.com>
 */
class DotEnvEditor
{
    /**
     * @var \SplFileObject|null
     */
    private $file;

    /**
     * @var array
     */
    private $dotEnvVariables = [];

    /**
     * @var string
     */
    private $filePath;

    public function __construct(?string $filePath = null)
    {
        @trigger_error('$filePath is currently optional but will be mandatory as of 1.2.0. Please update your application.', \E_USER_DEPRECATED);
        if (null !== $filePath) {
            $this->filePath = $filePath;
            $this->file = new \SplFileObject($filePath, 'r+');
        }
    }

    /**
     * Load .env.*.php variable in $dotEnvVariables property.
     * Construct a new file object.
     *
     * @param string|null $filePath The file path of the .env.*.php file
     */
    public function load(?string $filePath = null): void
    {
        if (null !== $filePath) {
            @trigger_error('Using the $filePath is deprecated since 1.1.0 and will be removed in 1.2.0. Use constructor instead.', \E_USER_DEPRECATED);
        }

        $this->dotEnvVariables = null === $filePath
            ? @include $this->filePath
            : @include $filePath;

        if (null === $this->file) {
            $this->file = new \SplFileObject($filePath, 'r+');
        }
    }

    /**
     * Add value to the .env.*.php available variables
     *
     * @param string                   $key   The key of the new variable
     * @param string|bool|integer|null $value The value of the new variable
     *
     * @throws DotEnvEditorAddVariableTypeException
     */
    public function add(string $key, $value = null)
    {
        if (!is_bool($value) && !is_string($value) && !is_null($value) && !is_int($value)) {
            throw new DotEnvEditorAddVariableTypeException($key, $value);
        }
        $this->dotEnvVariables[$key] = $value;
    }

    /**
     * Reset value for specified key.
     *
     * Value is set to empty string.
     *
     * @param string $key The key to reset
     */
    public function reset(string $key)
    {
        $this->dotEnvVariables[$key] = '';
    }

    /**
     * Save .env.*.php file to disk.
     *
     * @return int
     */
    public function save(): int
    {
        $content = $this->arrayToContent($this->dotEnvVariables);

        $this->file->rewind();
        $this->file->ftruncate(0);
        return $this->file->fwrite($content);
    }

    /**
     * Get the variable value for a specific key in the .env.*.php available variables.
     *
     * @param string $variableKey The key to get in the .env.*.php available variables
     * @return string|bool|int|null
     * @throws DotEnvEditorMissingVariableException When variable do not exists in the .env.*.php available variables
     */
    public function get(string $variableKey)
    {
        if (!array_key_exists($variableKey, $this->dotEnvVariables)) {
            throw new DotEnvEditorMissingVariableException($variableKey);
        }
        return $this->dotEnvVariables[$variableKey];
    }

    /**
     * Get variables as array.
     *
     * @return array Array of variables available in .env.*.php file
     */
    public function toArray(): array
    {
        return $this->dotEnvVariables;
    }

    /**
     * Convert an array to the content of .env.*.php file.
     *
     * @param array $array Array of variables available in .env.*.php file
     * @return string The string representing the content of .env.*.php file
     */
    private function arrayToContent(array $array): string
    {
        $contentParts = ['<?php', 'return array('];
        foreach ($array as $key => $value) {
            array_push($contentParts, sprintf($this->getOutputPatternForValue($value), $key, $value));
        }
        array_push($contentParts, ');');
        return implode(PHP_EOL, $contentParts);
    }

    /**
     * Get the output pattern according to the value value.
     *
     * @param mixed $value The value to check for the output pattern
     * @return string The output pattern for a specific value
     */
    private function getOutputPatternForValue($value): string
    {
        $outputPattern = '\'%s\'';
        if ($value === 'true' || $value === true) {
            $outputPattern = 'true';
        } elseif ($value === 'false' || $value === false) {
            $outputPattern = 'false';
        } elseif (is_null($value)) {
            $outputPattern = 'null';
        }

        return '\'%s\' => ' . $outputPattern . ',';
    }
}

