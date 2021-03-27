<?php

/*
 * This file is part of the dotenv-editor component.
 *
 * (c) Aurélien Tournayre <aurelien.tournayre@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Atournayre\Component\DotEnvEditor\Exception;

/**
 * @author Aurélien Tournayre <aurelien.tournayre@gmail.com>
 */
class DotEnvEditorMissingVariableException extends \Exception implements ExceptionInterface
{
    public function __construct(string $variable, string $message = '', int $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->message = sprintf('%s is missing in .env file!', $variable);
    }
}
