<?php

namespace Atournayre\Component\DotEnvEditor\Exception;

/**
 * @author Aurélien Tournayre <aurelien.tournayre@gmail.com>
 */
class DotEnvEditorAddVariableTypeException extends \Exception implements ExceptionInterface
{
    public function __construct(string $key, $value, string $message = '', int $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->message = sprintf('Only boolean or string values are allowed. "%s" is of type "%s".', $key, gettype($value));
    }
}
