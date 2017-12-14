<?php

use \DrewM\MailChimp\MailChimp;

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
        $data = simplexml_load_string($FoxyData);

        $apiKey = Config::inst()->get(static::class, 'apikey');
        $listName = Config::inst()->get(static::class, 'mailing_list_name');
	    $listID = Config::inst()->get(static::class, 'mailing_list_id');
        $useCustomField = Config::inst()->get(static::class, 'use_custom_field');
        $customFieldName = Config::inst()->get(static::class, 'custom_field_name');
        $customFieldValue = Config::inst()->get(static::class, 'custom_field_value');
        $sendConfirmation = Config::inst()->get(static::class, 'send_confirmation');
        $emailFormat = 'html';

        // if these are not provided error
        if (!($listName || $listID) || !$apiKey) {
            throw new LogicException('Both the MailChimp api key and the list name or id are required');
        }

        $mailChimp = new MailChimp($apiKey);

        if (!$listID) {
	        $lists = $mailChimp->get('lists');
	        $listID = -1;
	        foreach ($lists as $list) {
		        if (isset($list[0]['name']) && isset($list[0]['id'])) {
			        if ($list[0]['name'] == $listName) {
				        $listID = $list[0]['id'];
			        }
		        }
	        }
        }

        if ($listID === -1) {
            throw new LogicException('No list with that name was found');
        }

        foreach ($data->transactions->transaction as $tx) {
            $subscribe = !$useCustomField;
            if ($useCustomField && isset($tx->custom_fields->custom_field)) {
                foreach ($tx->custom_fields->custom_field as $field) {
                    $subscribe = $subscribe ||
                        ($field->custom_field_name == $customFieldName &&
                            $field->custom_field_value == $customFieldValue);
                }
            }


            if ($subscribe) {
                $response = $mailChimp->post("lists/$listID/members", [
                    // cast email_address to a string so its not a SimpleXMLElement object
                    'email_address' => (string) $tx->customer_email,
                    'merge_fields' => [
                        'FNAME' => (string) $tx->customer_first_name,
                        'LNAME' => (string) $tx->customer_last_name,
                    ],
                    'status' => $sendConfirmation ? 'pending' :'subscribed',
                    'email_type' => $emailFormat,
                ]);
                // TODO - do something with response?
	            // print_r($response);
            }
        }

        print "foxy";
    }
}
