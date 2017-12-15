<?php

/**
 * Class FoxyStripeMailChimpSiteConfig
 */
class FoxyStripeMailChimpSiteConfig extends DataExtension
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
		Requirements::javascript(Controller::join_links(FOXYSTRIPE_MAILCHIMP . '/javascript/mailchimp-entwine.js'));

		$mailingLists = MailChimpList::get()->map();
		$getSegments = function ($listID) {
			return MailChimpSegment::get()->filter(array(
				'MailingListID' => $listID
			))->map();
		};

		$fields->addFieldsToTab('Root.FoxyStripe.MailChimp', array(
			FormAction::create('updateMailing', 'Update MailChimp lists and segments'),
			$list = DropdownField::create('MailingListID', 'Mailing List', $mailingLists)
				->setEmptyString('Choose a mailing list'),
			DependentDropdownField::create('MailingSegmentID', 'Mailing Segment', $getSegments)
				->setDepends($list)
				->setEmptyString('Choose a mailing segment'),
		));
	}
}
