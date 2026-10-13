<?php

namespace App\Http\Requests\Admin;

/**
 * Same rules as creating a campaign (the slug rule already ignores the current campaign).
 */
class UpdateCampaignRequest extends StoreCampaignRequest
{
}
