<?php

namespace Elegance\Trait;

trait InputMessage
{
    protected static array $message = [
        FILTER_VALIDATE_IP => 'O campo [#] precisa ser um endereço IP',
        FILTER_VALIDATE_INT => 'O campo [#] precisa ser um numero inteiro',
        FILTER_VALIDATE_MAC => 'O campo [#] precisa ser um endereço MAC',
        FILTER_VALIDATE_URL => 'O campo [#] precisa ser uma URL',
        FILTER_VALIDATE_EMAIL => 'O campo [#] precisa ser um email',
        FILTER_VALIDATE_FLOAT => 'O campo [#] precisa ser um numero',
        FILTER_VALIDATE_DOMAIN => 'O campo [#] precisa ser um dominio',
        FILTER_VALIDATE_REGEXP => 'O campo [#] precisa ser um a expressão regular',
        FILTER_VALIDATE_BOOLEAN => 'O campo [#] precisa ser um valor booleano',
        'required' => 'O campo [#] é obrigatório',
        'preventTag' => 'O campo [#] contem um valor inválido',
        'default' => 'O campo [#] contem um erro'
    ];

    /** Adiciona ou altera uma mensagem do input */
    static function message(?string $message = null, ?string $value = null): array|string|null
    {
        return match (func_num_args()) {
            0 => self::$message,
            1 => self::$message[$message] ??
                $message ??
                self::$message['default'] ??
                'input error',
            default => self::$message[$message] = $value
        };
    }
}