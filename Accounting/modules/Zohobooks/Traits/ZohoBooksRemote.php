<?php

namespace Modules\Zohobooks\Traits;

use Weble\ZohoClient\OAuthClient;
use Webleit\ZohoBooksApi\Client;
use Webleit\ZohoBooksApi\ZohoBooks;

trait ZohoBooksRemote
{
    protected function getClient()
    {
        $oAuthClient = new OAuthClient(setting('zohobooks.client_id'), setting('zohobooks.client_secret'));
        $oAuthClient->setRefreshToken(setting('zohobooks.refresh_token'));
        if($oAuthClient->accessTokenExpired()) {
            $oAuthClient->offlineMode();

            setting()->set('zohobooks.token', $oAuthClient->getAccessToken());
            setting()->save();
        }

        $client = new Client($oAuthClient);
        $client->setRegion('*');
        $client->setOrganizationId(setting('zohobooks.organization_id'));

        return new ZohoBooks($client);
    }
}
