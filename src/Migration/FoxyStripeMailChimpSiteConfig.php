<?php

namespace Dynamic\FoxyStripeMailChimp\Migration;

use Dynamic\FoxyStripe\Model\FoxyStripeSetting;
use Dynamic\FoxyStripeMailChimp\Model\MailChimpList;
use Dynamic\FoxyStripeMailChimp\Model\MailChimpSegment;
use SilverStripe\ORM\DataExtension;

class FoxyStripeMailChimpSiteConfig extends DataExtension
{
    /**
     * @var array
     */
    private static $has_one = array(
        'MailingList' => MailChimpList::class,
        'MailingSegment' => MailChimpSegment::class,
    );

    /**
     *
     */
    public function onAfterWrite()
    {
        parent::onAfterWrite();

        $config = FoxyStripeSetting::current_foxystripe_setting();

        $config->MailingListID = $this->owner->MailingListID;
        $config->MailingSegment = $this->owner->MailingSegmentID;

        $config->write();
    }
}
