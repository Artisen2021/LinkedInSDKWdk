<?php
declare(strict_types=1);

namespace Artisen2021\LinkedInSDK\Distribution;

use Artisen2021\LinkedInSDK\Exception\CouldNotCreateACampaignGroup;
use Exception;

class CampaignGroup
{
    protected int $account_id;
    protected string $name;
    protected string $status;
    protected int $start;
    protected int $end;
    protected string $total_budget;
    protected string $currency;
    protected int $externalId;

    public function getExternalId(): int
    {
        return $this->externalId;
    }

    public function setExternalId($externalId): void
    {
        $this->externalId = $externalId;
    }

    public function getAccountId(): int
    {
        return $this->account_id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName($name): void
    {
        $this->name = $name;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus($status): void
    {
        $this->status = $status;
    }

    //TODO: Instead of returning dates as integers, may be good to use carbon object?
    public function getStart(): int
    {
        return $this->start;
    }

    public function setStart($start): void
    {
        $this->start = $start;
    }

    public function getEnd(): int
    {
        return $this->end;
    }

    public function setEnd($end): void
    {
        $this->end = $end;
    }

    public function getTotalBudget(): string
    {
        return $this->total_budget;
    }

    public function setTotalBudget($total_budget): void
    {
        $this->total_budget = $total_budget;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function setCurrency($currency): void
    {
        $this->currency = $currency;
    }
}