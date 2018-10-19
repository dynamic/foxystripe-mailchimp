<?php

namespace Dynamic\FoxyStripeMailChimp\Test\Model;

use Dynamic\FoxyStripeMailChimp\Model\MailChimpList;
use SilverStripe\Dev\SapphireTest;
use SilverStripe\Forms\FieldList;

class MailChimpListTest extends SapphireTest
{
    /**
     * @var string
     */
    protected static $fixture_file = 'fixtures.yml';

    /**
     *
     */
    public function testGetCMSFields()
    {
        $object = $this->objFromFixture(MailChimpList::class, 'one');
        $fields = $object->getCMSFields();
        $this->assertInstanceOf(FieldList::class, $fields);
    }

}