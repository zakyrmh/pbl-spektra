<?php

declare(strict_types=1);

namespace App\Exceptions\Public;

use Exception;

final class FeedbackValidationException extends Exception
{
    public function __construct(
        string $message,
        private string $alertType = 'error',
        int $code = 400,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }

    public function getAlertType(): string
    {
        return $this->alertType;
    }
}
