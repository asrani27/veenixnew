<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        'admin/movies/tus-upload*',
        'admin/movies/tus-upload',
        'admin/movies/tus-upload/*',
    ];
}

// Register the static exemption
VerifyCsrfToken::except([
    'admin/movies/tus-upload*',
    'admin/movies/tus-upload',
    'admin/movies/tus-upload/*',
]);
