<?php

namespace Modules\Quickbooks\Http\Middleware;

use Closure;
use QuickBooksOnline\API\DataService\DataService;

class QuickbooksRefreshToken
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $dataService = DataService::Configure(array(
            'auth_mode' => 'oauth2',
            'ClientID' => setting('quickbooks.client_id'),
            'ClientSecret' => setting('quickbooks.client_secret'),
            'accessTokenKey' => setting('quickbooks.token'),
            'refreshTokenKey' => setting('quickbooks.refresh_token'),
            'QBORealmID' => setting('quickbooks.realm_id'),
            'baseUrl' => setting('quickbooks.environment')
        ));
        $OAuth2LoginHelper = $dataService->getOAuth2LoginHelper();
        $accessToken = $OAuth2LoginHelper->refreshToken();

        setting()->set('quickbooks.token', $accessToken->getAccessToken());
        setting()->set('quickbooks.refresh_token', $accessToken->getRefreshToken());
        setting()->save();

        return $next($request);
    }
}
