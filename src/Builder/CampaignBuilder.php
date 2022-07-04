<?php

namespace Artisen2021\LinkedInSDK\Builder;

class CampaignBuilder
{
    public function create(array $params): array
    {
        return [
            'account' => 'urn:li:sponsoredAccount:'.$params['account_id'],
            'campaignGroup' => 'urn:li:sponsoredCampaignGroup:'.$params['campaign_group_id'],
            'costType' => $params['costType'],
            'dailyBudget' => [
                'amount' => $params['dailyBudget']['amount'],
                'currencyCode' => $params['dailyBudget']['currencyCode'],
            ],
            'locale' => [
                'country' => $params['country'],
                'language' => $params['language'],
            ],
            'name' => $params['name'],
            'runSchedule' => [
                'start' => $params['start'],
                'end' => $params['end'],
            ],
            'targetingCriteria' => [
                'include' => [
                    'and' => [
                        [
                            'or' => [
                                'urn:li:adTargetingFacet:locations' => $params['locations'],
                            ],
                        ],
                    ],
                ],
            ],
            'type' => $params['type'],
            'unitCost' => [
                'amount' => $params['unitCost']['amount'],
                'currencyCode' => $params['unitCost']['currencyCode'],
            ],
            'status' => $params['status']
        ];
    }

    public function update($params): array
    {
        return [
            'patch' => [
                '$set' => $params,
            ],
        ];
    }

    public function delete(): array
    {
        return [
            'patch' => [
                '$set' => [
                    'status' => 'ARCHIVED',
                ],
            ],
        ];
    }
}