<?php

namespace Dynamic\FoxyStripeMailChimp\Model;

use SilverStripe\ORM\DataObject;

/**
 * Class MailChimpSegment
 */
class MailChimpSegment extends DataObject
{
    private static $db = array(
        'Title' => 'Varchar(255)',
        'MCID' => 'Varchar(255)',
    );

    private static $has_one = array(
        'MailingList' => MailChimpList::class,
    );

    private static $indexes = array(
        'MCID' => array(
            'type' => 'unique',
            //'value' => 'MCID',
        ),
    );

    private static $table_name = 'MailChimpSegment';
}
