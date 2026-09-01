<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use PHPOpenSourceSaver\JWTAuth\Http\Parser\AuthHeaders;

/**
 * Restricts JWT parsing to the Authorization header.
 *
 * jwt-auth ships with a parser chain of AuthHeaders -> QueryString -> InputSource,
 * which means "?token=<jwt>" (or a "token" form field) authenticates any API call.
 * A token in a URL leaks through browser history, Referer headers, proxy logs and
 * anything a user copies and pastes - so a shared link becomes a full session.
 */
class JwtParserServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->app['tymon.jwt.parser']->setChain([
            new AuthHeaders(),
        ]);
    }
}
