<?php

namespace Artisen2021\LinkedInSDK\Builder;

class CampaignGroupBuilder
{
    public function create(array $params): array
    {
        return [
            'account' => 'urn:li:sponsoredAccount:' . $params['account_id'],
            'name' => $params['name'],
            'runSchedule' => [
                'start' => $params['start'],
                'end' => $params['end'],
            ],
            'status' => 'ACTIVE',
            'totalBudget' => [
                'amount' => $params['total_budget'],
                'currencyCode' => $params['currency'],
            ],
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