<?php

namespace Dynamic\FoxyStripeMailChimp\ORM;

use Dynamic\FoxyStripeMailChimp\Model\MailChimpList;
use Dynamic\FoxyStripeMailChimp\Model\MailChimpSegment;
use SilverStripe\Forms\CompositeField;
use SilverStripe\Forms\FieldList;
use SilverStripe\View\Requirements;
use SilverStripe\Forms\FormAction;
use SilverStripe\Forms\DropdownField;
use SilverStripe\ORM\DataExtension;
use Sheadawson\DependentDropdown\Forms\DependentDropdownField;

/**
 * Class FoxyStripeMailChimpSiteConfig
 */
class FoxyStripeMailChimpSettingExtension extends DataExtension
{

    private static $db = array(
        'DoubleOptIn' => 'Boolean',
    );

    private static $has_one = array(
        'MailingList' => MailChimpList::class,
        'MailingSegment' => MailChimpSegment::class,
    );

    public function updateCMSFields(FieldList $fields)
    {
        Requirements::css( 'dynamic/foxystripe-mailchimp: client/css/style.css');
        Requirements::javascript('dynamic/foxystripe-mailchimp: client/js/mailchimp-entwine.js');

        $mailingLists = MailChimpList::get()->map();
        $getSegments = function ($listID) {
            return MailChimpSegment::get()->filter(array(
                'MailingListID' => $listID
            ))->map();
        };

        $fields->addFieldsToTab('Root.MailChimp', array(
            CompositeField::create(
                FormAction::create('updateMailing', 'Update MailChimp lists and segments')
                    ->setUseButtonTag(true)
                    ->addExtraClass('font-icon-sync')
            ),
            $list = DropdownField::create('MailingListID', 'Mailing List', $mailingLists)
                ->setEmptyString('Choose a mailing list'),
            DependentDropdownField::create('MailingSegmentID', 'Mailing Segment', $getSegments)
                ->setDepends($list)
                ->setEmptyString('Choose a mailing segment')
                ->setRightTitle('Only shows static segments'),
        ));
    }
}
