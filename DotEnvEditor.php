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

use Atournayre\Component\Exception\DotEnvEditorMissingVariableException;

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

    public function load(string $filePath): void
    {
        $this->dotEnvVariables = @include $filePath;
        $this->file = new \SplFileObject($filePath, 'r+');
    }

    public function add(string $key, ?string $value)
    {
        $this->dotEnvVariables[$key] = $value;
    }

    public function reset(string $key)
    {
        $this->dotEnvVariables[$key] = '';
    }

    public function save(): int
    {
        $content = $this->arrayToContent($this->dotEnvVariables);

        $this->file->rewind();
        $this->file->ftruncate(0);
        return $this->file->fwrite($content);
    }

    /**
     * @param $variableKey
     * @return string
     * @throws DotEnvEditorMissingVariableException
     */
    public function get($variableKey): string
    {
        if (!array_key_exists($variableKey, $this->dotEnvVariables)) {
            throw new DotEnvEditorMissingVariableException($variableKey);
        }
        return $this->dotEnvVariables[$variableKey];
    }

    public function toArray(): array
    {
        return $this->dotEnvVariables;
    }

    private function arrayToContent(array $array): string
    {
        $contentParts = ['<?php', 'return array('];
        foreach ($array as $key => $value) {
            array_push($contentParts, sprintf($this->getOutputPatternForValue($value), $key, $value));
        }
        array_push($contentParts, ');');
        return implode(PHP_EOL, $contentParts);
    }

    private function getOutputPatternForValue($value)
    {
        $outputPattern = '\'%s\' => \'%s\',';
        if ($value === 'true' || $value === true) {
            $outputPattern = '\'%s\' => true,';
        } elseif ($value === 'false' || $value === false) {
            $outputPattern = '\'%s\' => false,';
        } elseif (is_null($value)) {
            $outputPattern = '\'%s\' => null,';
        }

        return '    ' . $outputPattern . PHP_EOL;
    }
}

