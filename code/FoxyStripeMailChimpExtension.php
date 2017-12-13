<?php

require 'fox2chimp/MailChimpUtils.php';

/**
 * Class FoxyStripeMailChimpExtension
 */
class FoxyStripeMailChimpExtension extends Extension
{

	/**
	 * Modified from fox2chimp/fc-mailchimp.php
	 *
	 * @param $dataFeed
	 */
	public function addIntegrations($dataFeed)
	{
		$FoxyData = rc4crypt::decrypt(FoxyCart::getStoreKey(), $dataFeed);
		$data = new XMLParser($FoxyData);
		$data->Parse();

		$MailChimp_Auth = array(
			'apikey' => Config::inst()->get(static::class, 'apikey'),
		);

		$Use_Custom_Field = Config::inst()->get(static::class, 'use_custom_field');

		$Custom_Field = Config::inst()->get(static::class, 'custom_field_name');
		$Custom_Field_Value = Config::inst()->get(static::class, 'custom_field_value');

		// The customer's preferred email format.
		$Email_Format = 'html';

		// send a confirmation?
		$Send_Confirmation = Config::inst()->get(static::class, 'send_confirmation');

		$List_Name = Config::inst()->get(static::class, 'mailing_list_name');

		// if these are not provided error
		if (!$List_Name || !$MailChimp_Auth) {
			throw new LogicException('Both the MailChimp api key and the list name are required');
		}

		foreach ($data->document->transactions[0]->transaction as $tx) {
			$subscribe = !$Use_Custom_Field;
			if ($Use_Custom_Field && isset($tx->custom_fields[0]->custom_field)) {
				foreach ($tx->custom_fields[0]->custom_field as $field) {
					$subscribe = $subscribe ||
					             ($field->custom_field_name[0]->tagData == $Custom_Field &&
					              $field->custom_field_value[0]->tagData == $Custom_Field_Value);
				}
			}

			if ($subscribe) {
				// See MailChimpUtils.php for documentation.
				subscribe_user_to_list(
					array(
						'first_name' => $tx->customer_first_name[0]->tagData,
						'last_name' => $tx->customer_last_name[0]->tagData,
						'email' => $tx->customer_email[0]->tagData,
						'format' => $Email_Format,
						'confirm' => $Send_Confirmation),
					$List_Name,
					$MailChimp_Auth);
			}
		}

		print "foxy";
	}

}
