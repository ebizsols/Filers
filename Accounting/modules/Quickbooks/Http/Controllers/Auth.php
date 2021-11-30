<?php

namespace Modules\Quickbooks\Http\Controllers;

use App\Abstracts\Http\Controller;
use QuickBooksOnline\API\DataService\DataService;

class Auth extends Controller
{
    public $dataService;

    public function OAuth()
    {
        $this->fireEvent('Attempting');

        $company_id = session('quickbooks_company_id');

        session('quickbooks_state');
        if (empty($company_id)) {
            flash('Missing company id')->error();

            return redirect()->route('quickbooks.settings.edit');
        }
        if(is_null(setting('quickbooks.environment'))) {
            setting()->set('quickbooks.environment', 'production');
        }
        company($company_id)->makeCurrent();

        $this->dataService = DataService::Configure(array(
            'auth_mode' => 'oauth2',
            'ClientID'     => setting('quickbooks.client_id'),
            'ClientSecret' => setting('quickbooks.client_secret'),
            'RedirectURI' => route('quickbooks.auth.start'),
            'scope' => "com.intuit.quickbooks.accounting",
            'baseUrl' => setting('quickbooks.environment')
        ));
//        session([
//            'quickbooks_state' => $_GET['state'],
//        ]);
        if (!isset($_GET['code'])) {
            $OAuth2LoginHelper = $this->dataService->getOAuth2LoginHelper();
            $authUrl = $OAuth2LoginHelper->getAuthorizationCodeURL();

            $parts = parse_url($authUrl);
            parse_str($parts['query'], $query);

            session([
                'quickbooks_state' => $query['state'],
            ]);
            return redirect($authUrl);
        } elseif (empty($_GET['state']) || ($_GET['state'] !== session('quickbooks_state'))) {

            session([
                'quickbooks_state' => false,
            ]);

            exit('Invalid state');
        } else {
            try {
                $OAuth2LoginHelper = $this->dataService->getOAuth2LoginHelper();

                $token = $OAuth2LoginHelper->exchangeAuthorizationCodeForToken($_GET['code'], $_GET['realmId']);

                setting()->set([
                    'quickbooks.token'                          => $token->getAccessToken(),
                    'quickbooks.refresh_token'                  => $token->getRefreshToken(),
                    'quickbooks.refresh_token_expires_at'       => $token->getRefreshTokenExpiresAt(),
                    'quickbooks.realm_id'                       => $token->getRealmID(),
                    'quickbooks.enabled'                        => true,
                ]);
                setting()->save();

                $message = trans('quickbooks::general.auth_success');

                flash($message)->success();

                $this->fireEvent('Authenticated');
            } catch (\Throwable $e) {
                $this->fireEvent('Failed');

                logger('Quickbooks auth failed:: ' . $e->getMessage());

                $message = trans('quickbooks::general.auth_failed', ['error' => $e->getMessage()]);

                flash($message)->error();
            }

            session()->forget('quickbooks_company_id');

            return redirect()->route('quickbooks.settings.edit', ['company_id' => $company_id]);
        }
    }

    protected function fireEvent($name)
    {
        $class = '\App\Events\Auth\\' . $name;

        if (!class_exists($class)) {
            return;
        }

        event(new $class('quickbooks', company_id(), 'oauth'));
    }
}
