<?php

namespace Modules\Zohobooks\Http\Controllers;

use App\Abstracts\Http\Controller;
use Weble\ZohoClient\OAuthClient;

class Auth extends Controller
{
    public $dataService;

    public function __construct()
    {
        config(['firewall.middleware.rfi.routes.except' => ['zohobooks/auth']]);
    }

    public function OAuth()
    {
        $this->fireEvent('Attempting');

        $company_id = session('zohobooks_company_id');

        if (empty($company_id)) {
            flash('Missing company id')->error();

            return redirect()->route('zohobooks.settings.edit');
        }
        company($company_id)->makeCurrent();

        $client = new OAuthClient(setting('zohobooks.client_id'), setting('zohobooks.client_secret'), '*', url('/zohobooks/auth'));
        $client->offlineMode();

        if(isset($_GET['state'])) {
            session([
                'zohobooks_state' => $_GET['state'],
            ]);
        }
        if (!isset($_GET['code'])) {
            // Get the url
            $client->setScopes(['ZohoBooks.fullaccess.all']);

            session()->forget('zohobooks_state');

            session([
                'zohobooks_state' => $client->getState(),
            ]);

            return redirect($client->getAuthorizationUrl());

            // Check given state against previously stored one to mitigate CSRF attack
        } elseif (empty($_GET['state']) || ($_GET['state'] !== session('zohobooks_state'))) {

            session()->forget('zohobooks_state');

            exit('Invalid state');
        } else {
            // Try to get an access token (using the authorization code grant)
            try {
                $client->setGrantCode($_GET['code']);

                setting()->set([
                    'zohobooks.refresh_token'                  => $client->getRefreshToken(),
                    'zohobooks.token'                          => $client->getAccessToken(),
                    'zohobooks.enabled'                        => true,
                    'zohobooks.code'                           => $_GET['code'],
                ]);
                setting()->save();

                $message = trans('zohobooks::general.auth_success');

                flash($message)->success();

                $this->fireEvent('Authenticated');


            } catch (\Exception $e) {
                $this->fireEvent('Failed');

                logger('ZohoBooks auth failed:: ' . $e->getMessage());

                $message = trans('zohobooks::general.auth_failed', ['error' => $e->getMessage()]);

                flash($message)->error();
            }

            session()->forget('zohobooks_company_id');

            return redirect()->route('zohobooks.settings.edit', ['company_id' => $company_id]);
        }
    }

    protected function fireEvent($name)
    {
        $class = '\App\Events\Auth\\' . $name;

        if (!class_exists($class)) {
            return;
        }

        event(new $class('zohobooks', company_id(), 'oauth'));
    }
}
