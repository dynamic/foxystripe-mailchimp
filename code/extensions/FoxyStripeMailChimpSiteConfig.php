<?php

use \DrewM\MailChimp\MailChimp;

/**
 * Class FoxyStripeMailChimpSiteConfig
 */
class FoxyStripeMailChimpSiteConfig extends DataExtension
{

	private static $db = array(
		'MailingList' => 'Varchar(255)',
		'MailingSegment' => 'Varchar(255)',
		'DoubleOptIn' => 'Boolean',
	);

	public function updateCMSFields(FieldList $fields)
	{
		Requirements::javascript(Controller::join_links(FOXYSTRIPE_MAILCHIMP . '/javascript/mailchimp-entwine.js'));

		$mailingLists = MailChimpList::get()->map();
		$getSegments = function ($listID) {
			return MailChimpSegment::get()->filter(array(
				'MailingListID' => $listID
			))->map();
		};

		$fields->addFieldsToTab('Root.FoxyStripe.MailChimp', array(
			FormAction::create('updateMailing', 'Update MailChimp lists and segments'),
			$list = DropdownField::create('MailingList', 'Mailing List', $mailingLists),
			DependentDropdownField::create('MailingSegment', 'Mailing Segment', $getSegments)->setDepends($list),
		));
	}
}
