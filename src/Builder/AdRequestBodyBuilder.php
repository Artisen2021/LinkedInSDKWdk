<?php
declare(strict_types=1);


namespace Artisen2021\LinkedInSDK\Builder;

class AdRequestBodyBuilder
{

    private const OWNER = 'urn:li:organization:';

    //https://docs.microsoft.com/en-us/linkedin/marketing/integrations/community-management/shares/share-api?view=li-lms-unversioned&tabs=http#direct-sponsored-content-share
    public function createDarkShareForImageAd(array $params): array
    {
        return [
            'agent' => 'urn:li:sponsoredAccount:' . $params['account_id'],
            'content' => [
                'contentEntities' => [
                    [
                        //'landingPageTitle' => strtoupper(str_replace(' ', '_', $params['call_to_action'])),
                        'landingPageUrl' => $params['landing_page_url'],
                        'description' => $params['text'],
                        'title' => $params['title'],
                        'entityLocation' => $params['landing_page_url'],
                        'thumbnails' => [
                            [
                                'resolvedUrl' => $params['media_url'],
                            ],
                        ],
                    ],
                ],
            ],
            'owner' => self::OWNER . $params['page_id'],
            'subject' => $params['title'],
            'text' => [
                'text' => $params['text'],
            ],
        ];
    }

    public function createImageAdRequest(array $params): array
    {
        return [
            'campaign' => 'urn:li:sponsoredCampaign:' . $params['campaign_id'],
            'reference' => 'urn:li:share:' . $params['share_reference'],
            'status' => 'ACTIVE',
            'type' => 'SPONSORED_STATUS_UPDATE',
            'variables' => [
                'data' => [
                    'com.linkedin.ads.SponsoredUpdateCreativeVariables' => [
                        'activity' => $params['activity'],
                    ],
                ],
            ],
        ];
    }

    //https://docs.microsoft.com/en-us/linkedin/marketing/integrations/community-management/shares/vector-asset-api?view=li-lms-unversioned&tabs=http
    public function requestCredentialsForVideoUpload(string $pageId): array
    {
        return [
            'registerUploadRequest' => [
                'owner' => self::OWNER . $pageId,
                'recipes' => [
                    'urn:li:digitalmediaRecipe:ads-video',
                ],
                'serviceRelationships' => [
                    [
                        'identifier' => 'urn:li:userGeneratedContent',
                        'relationshipType' => 'OWNER',
                    ],
                ],
            ],
        ];
    }


    public function createDarkShareForVideoAd(array $params): array
    {
        return [
            'author' => self::OWNER . $params['page_id'],
            'lifecycleState' => 'PUBLISHED',
            'specificContent' => [
                'com.linkedin.ugc.ShareContent' => [
                    'media' => [
                        [
                            'landingPage' => [
                                'landingPageTitle' => strtoupper(str_replace(' ', '_', $params['call_to_action'])),
                                'landingPageUrl' => $params['landing_page_url'],
                            ],
                            'media' => $params['media'],
                            'status' => 'READY',
                            'title' => [
                                'text' => $params['title'],
                            ],
                        ],
                    ],
                    'shareCommentary' => [
                        'text' => $params['text'],
                    ],
                    'shareMediaCategory' => 'VIDEO',
                ],
            ],
            'visibility' => [
                'com.linkedin.ugc.SponsoredContentVisibility' => 'DARK',
            ],
        ];
    }

    public function createAdDirectSponsoredContent($params): array
    {
        return [
            'account' => 'urn:li:sponsoredAccount:' . $params['account_id'],
            'contentReference' => $params['post_reference'],
            'name' => $params['title'],
            'owner' => self::OWNER . $params['page_id'],
            'type' => 'VIDEO',
        ];
    }

    public function createVideoAdRequest(array $params): array
    {
        return [
            'campaign' => 'urn:li:sponsoredCampaign:' . $params['campaign_id'],
            'reference' => $params['direct_share_reference'],
            'status' => 'ACTIVE',
            'type' => 'SPONSORED_VIDEO',
        ];
    }

}
