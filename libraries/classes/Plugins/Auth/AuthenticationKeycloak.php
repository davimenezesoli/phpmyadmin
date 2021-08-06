<?php

declare(strict_types=1);

namespace PhpMyAdmin\Plugins\Auth;

use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Token;
use PhpMyAdmin\Plugins\AuthenticationPlugin;

/**
 * Class AuthenticationKeycloak
 * @package PhpMyAdmin\Plugins\Auth
 */
class AuthenticationKeycloak extends AuthenticationPlugin
{
    public function showLoginForm(): bool
    {
        return false;
    }

    public function readCredentials(): bool
    {
        if (!isset($_COOKIE["kc-access"])) {
            return false;
        }

        $keycloakToken = $_COOKIE["kc-access"];
        if (!$keycloakToken) {
            return false;
        }

        $token = $this->getToken($keycloakToken);
        if (!$token->getClaim('preferred_username') || !$token->getClaim('sub')) {
            return false;
        }

        $this->user = $token->getClaim('preferred_username');
        $this->password = $token->getClaim('sub');
        
        return true;
    }

    /**
     * @param string $keycloakToken
     * @return Token
     */
    protected function getToken(string $keycloakToken): Token
    {
        $parser = new Parser();
        return $parser->parse($keycloakToken);
    }
}
