<?php

namespace Dynamic\FoxyStripeMailChimp\ORM;

use \DrewM\MailChimp\MailChimp;
use Dynamic\FoxyStripe\Model\FoxyCart;
use Dynamic\FoxyStripe\Model\FoxyStripeSetting;
use SilverStripe\SiteConfig\SiteConfig;
use SilverStripe\Core\Config\Config;
use SilverStripe\Core\Extension;

/**
 * Class FoxyStripeMailChimpExtension
 */
class FoxyStripeMailChimpExtension extends Extension
{

    /**
     * @param $dataFeed
     */
    public function addIntegrations($dataFeed)
    {
        $FoxyData = \rc4crypt::decrypt(FoxyCart::getStoreKey(), $dataFeed);
        $data = simplexml_load_string($FoxyData);

        $config = FoxyStripeSetting::current_foxystripe_setting();
        $list = $config->MailingList();
        $segment = $config->MailingSegment();

        $apiKey = Config::inst()->get(static::class, 'apikey');
        $listID = $list->MCID;
        $segmentID = $segment->MCID;

        $useCustomField = Config::inst()->get(static::class, 'use_custom_field');
        $customFieldName = Config::inst()->get(static::class, 'custom_field_name');
        $customFieldValue = Config::inst()->get(static::class, 'custom_field_value');
        $doubleOpt = $config->DoubleOptIn;
        $emailFormat = 'html';

        // if these are not provided error
        if (!$listID || !$apiKey) {
            throw new \LogicException('Both the MailChimp api key and the list name or id are required');
        }

        $mailChimp = new MailChimp($apiKey);

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
                $batch = $mailChimp->new_batch();

                $batch->post("add_user", "lists/$listID/members", [
                    // cast email_address to a string so its not a SimpleXMLElement object
                    'email_address' => (string) $tx->customer_email,
                    'merge_fields' => [
                        'FNAME' => (string) $tx->customer_first_name,
                        'LNAME' => (string) $tx->customer_last_name,
                    ],
                    'status' => $doubleOpt ? 'pending' :'subscribed',
                    'email_type' => $emailFormat,
                ]);

                if ($segmentID) {
                    $batch->post("add_user_to_segment", "lists/$listID/segments/$segmentID/members", [
                        'email_address' => (string) $tx->customer_email,
                    ]);
                }

                $result = $batch->execute();
            }
        }
    }
}
