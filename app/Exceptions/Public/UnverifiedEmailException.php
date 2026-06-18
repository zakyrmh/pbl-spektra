<?php

declare(strict_types=1);

namespace App\Exceptions\Public;

use Exception;

final class UnverifiedEmailException extends Exception
{
    public function __construct(
        public readonly string $email,
        string $message = 'Email Anda belum terverifikasi. Silakan cek email Anda untuk melakukan verifikasi.'
    ) {
        parent::__construct($message);
    }
}
