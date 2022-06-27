<?php

namespace Artisen2021\LinkedInSDK\Http;

use Artisen2021\LinkedInSDK\Authentication\Client;
use Artisen2021\LinkedInSDK\Event\LinkedInGetPageCurrentStatusEvent;
use Artisen2021\LinkedInSDK\Event\LinkedInGetPageDataEvent;
use Artisen2021\LinkedInSDK\Event\LinkedInRequestAdAccountsEvent;
use Artisen2021\LinkedInSDK\Event\LinkedInRequestPendingPagesEvent;
use Artisen2021\LinkedInSDK\Exception\CouldNotGetPageDataEvent;
use Artisen2021\LinkedInSDK\Exception\CouldNotGetPendingClientPages;
use Artisen2021\LinkedInSDK\Exception\PermissionCouldNotBeCreated;
use Artisen2021\LinkedInSDK\UrlEnums;
use GuzzleHttp\Exception\RequestException;
use JsonException;

class SocialPageRequest extends LinkedInRequest
{
    use TraitOAuthIsSetUp;

    private const BEARER = 'Bearer: ';
    public const HEADER_RESOURCE_ID = 'X-LinkedIn-Id';
    public Client $client;
    public string $token;

    public function __construct()
    {
        $this->client = $this->getSetUpClient();
        $this->token = $this->getSetUpToken();
    }

    /**
     * @throws CouldNotGetPendingClientPages
     */
    public function getPendingClientPages(LinkedInRequestPendingPagesEvent $event): ?array
    {
        $uri = $this->client->buildUrl(UrlEnums::URL['PENDING_CLIENT_PAGES'], []);

        $header = ['Authorization' => self::BEARER . $this->token];

        try {
            $request = new LinkedInRequest();
            $response = $request->send('GET', $uri, $header, []);
        } catch (PermissionCouldNotBeCreated $e) {
            throw new CouldNotGetPendingClientPages($e->getMessage(), $e->getCode());
        }
        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * @throws CouldNotGetPageDataEvent
     * @throws JsonException
     */
    public function getPageData(LinkedInGetPageDataEvent $event): ?array
    {
        $queryFields = [
            'primaryOrganizationType',
            'vanityName',
            'localizedName',
            'logoV2(cropped~:playableStreams,cropInfo)'
        ];
        $url = sprintf(
            'organizations/%s?projection=(%s)',
            $event->getPageId(),
            implode(',', $queryFields)
        );
        $header = ['Authorization' => self::BEARER . $this->token];

        try {
            $request = new LinkedInRequest();
            $response = $request->send('GET', $url, $header, []);
        } catch (PermissionCouldNotBeCreated $e) {
            throw new CouldNotGetPageDataEvent($e->getMessage(), $e->getCode());
        }
        return json_decode($response->getBody()->getContents(),
            true,
            512,
            JSON_THROW_ON_ERROR
        );
    }

    /**
     * @throws CouldNotGetPageDataEvent
     * @throws JsonException
     */
    public function getPageCurrentStatus(LinkedInGetPageCurrentStatusEvent $event): ?array
    {
        $organizationUrn = sprintf('urn:li:organization:%s',$event->getPageId());
        $url = sprintf(
            'organizationAcls?q=organization&organization=%s&role=ADMINISTRATOR&state=APPROVED',
            $organizationUrn
        );
        $header = ['Authorization' => self::BEARER . $this->token];

        try {
            $request = new LinkedInRequest();
            $response = $request->send('GET', $url, $header, []);
        } catch (PermissionCouldNotBeCreated $e) {
            throw new CouldNotGetPageDataEvent($e->getMessage(), $e->getCode());
        }
        return json_decode($response->getBody()->getContents(),
            true,
            512,
            JSON_THROW_ON_ERROR
        );
    }

    /**
     * @throws CouldNotGetPageDataEvent
     * @throws JsonException
     */
    public function searchAdAccountsByPageId(LinkedInRequestAdAccountsEvent $event): ?array
    {
        $organisationUrn = sprintf('urn:li:organization:%s', $event->getPageId());
        $url = sprintf('adAccountsV2?q=search&search.reference.values[0]=%s', $organisationUrn);
        $header = ['Authorization' => self::BEARER . $this->token];

        try {
            $request = new LinkedInRequest();
            $response = $request->send('GET', $url, $header, []);
        } catch (PermissionCouldNotBeCreated $e) {
            throw new CouldNotGetPageDataEvent($e->getMessage(), $e->getCode());
        }
        return json_decode($response->getBody()->getContents(),
            true,
            512,
            JSON_THROW_ON_ERROR
        );
    }
}