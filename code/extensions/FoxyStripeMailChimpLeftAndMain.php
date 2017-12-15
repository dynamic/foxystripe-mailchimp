<?php

use \DrewM\MailChimp\MailChimp;

/**
 * Class FoxyStripeMailChimpLeftAndMain
 */
class FoxyStripeMailChimpLeftAndMain extends LeftAndMainExtension
{

	private static $allowed_actions = array(
		'updateMailing'
	);

	// TODO - remove old lists / segments
	public function updateMailing() {
		$apiKey = Config::inst()->get(FoxyStripeMailChimpExtension::class, 'apikey');
		$mailChimp = new MailChimp($apiKey);


		$lists = $mailChimp->get('lists')['lists'];
		foreach ($lists as $list) {
			$listID = $list['id'];
			$current = MailChimpList::get()->where(array(
				'MCID' => $listID
			))->first();

			if ($current == null || !$current->exists()) {
				$current = MailChimpList::create();
				$current->MCID = $listID;
				$current->Title = $list['name'];
				$current->write();
			} else {
				$current->Title = $list['name'];
				$current->write();
			}

			$segments = $mailChimp->get("lists/$listID/segments")['segments'];
			foreach ($segments as $segment) {
				$segmentID = $segment['id'];

				$currentSegment = MailChimpSegment::get()->where(array(
					'MCID' => $segmentID
				))->first();

				if ($currentSegment == null || !$currentSegment->exists()) {
					$currentSegment = MailChimpSegment::create();
					$currentSegment->MCID = $segmentID;
					$currentSegment->Title = $segment['name'];
					$currentSegment->MailingListID = $current->ID;
					$currentSegment->write();
				} else {
					$currentSegment->Title = $segment['name'];
					$currentSegment->write();
				}
			}
		}
	}
}
