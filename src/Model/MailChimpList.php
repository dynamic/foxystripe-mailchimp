<?php

namespace Dynamic\FoxyStripeMailChimp\Model;

use SilverStripe\ORM\DataObject;

/**
 * Class MailChimpList
 */
class MailChimpList extends DataObject
{
    private static $db = array(
        'Title' => 'Varchar(255)',
        'MCID' => 'Varchar(255)',
    );

    private static $has_many = array(
        'MailingSegments' => MailChimpSegment::class,
    );

    private static $indexes = array(
        'MCID' => array(
            'type' => 'unique',
            'columns' => array(
                'MCID',
            ),
        ),
    );

    private static $table_name = 'MailChimpList';
}
