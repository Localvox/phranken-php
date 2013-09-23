<?php
namespace MarketMeSuite\Phranken\Util;

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.0 on 2013-06-03 at 16:37:51.
 */
class UtilsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Utils
     */
    protected $object;


    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new Utils;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * Generated from @assert ('bigtallbill@gmail.com') === true.
     *
     * @covers MarketMeSuite\Phranken\Util\Utils::checkEmailAddress
     */
    public function testCheckEmailAddress()
    {
        $this->assertSame(
            true,
            Utils::checkEmailAddress('bigtallbill@gmail.com')
        );
    }

    /**
     * Generated from @assert ('blah') === false.
     *
     * @covers MarketMeSuite\Phranken\Util\Utils::checkEmailAddress
     */
    public function testCheckEmailAddress2()
    {
        $this->assertSame(
            false,
            Utils::checkEmailAddress('blah')
        );
    }

    /**
     * Generated from @assert (null) === false.
     *
     * @covers MarketMeSuite\Phranken\Util\Utils::checkEmailAddress
     */
    public function testCheckEmailAddress3()
    {
        $this->assertSame(
            false,
            Utils::checkEmailAddress(null)
        );
    }

    /**
     * Generated from @assert (0) === false.
     *
     * @covers MarketMeSuite\Phranken\Util\Utils::checkEmailAddress
     */
    public function testCheckEmailAddress5()
    {
        $this->assertSame(
            false,
            Utils::checkEmailAddress(0)
        );
    }

    /**
     * Generated from @assert (true) === false.
     *
     * @covers MarketMeSuite\Phranken\Util\Utils::checkEmailAddress
     */
    public function testCheckEmailAddress6()
    {
        $this->assertSame(
            false,
            Utils::checkEmailAddress(true)
        );
    }

    /**
     * Generated from @assert (false) === false.
     *
     * @covers MarketMeSuite\Phranken\Util\Utils::checkEmailAddress
     */
    public function testCheckEmailAddress7()
    {
        $this->assertSame(
            false,
            Utils::checkEmailAddress(false)
        );
    }

    /**
     * @covers MarketMeSuite\Phranken\Util\Utils::xmlEncode
     * @todo   Implement testXmlEncode().
     */
    public function testXmlEncode()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers MarketMeSuite\Phranken\Util\Utils::utf8EncodeAll
     * @todo   Implement testUtf8EncodeAll().
     */
    public function testUtf8EncodeAll()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers MarketMeSuite\Phranken\Util\Utils::grabDump
     * @todo   Implement testGrabDump().
     */
    public function testGrabDump()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers MarketMeSuite\Phranken\Util\Utils::checkForUrl
     * @todo   Implement testCheckForUrl().
     */
    public function testCheckForUrl()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers MarketMeSuite\Phranken\Util\Utils::EpochToPrettyDate
     * @todo   Implement testEpochToPrettyDate().
     */
    public function testEpochToPrettyDate()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers MarketMeSuite\Phranken\Util\Utils::below
     * @todo   Implement testBelow().
     */
    public function testBelow()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers MarketMeSuite\Phranken\Util\Utils::between
     * @todo   Implement testBetween().
     */
    public function testBetween()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers MarketMeSuite\Phranken\Util\Utils::hasProperties
     * @todo   Implement testHasProperties().
     */
    public function testHasProperties()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers MarketMeSuite\Phranken\Util\Utils::JsonPrettyPrint
     */
    public function testJsonPrettyPrint()
    {
        $json = json_encode(array('foo'=>'bar','hello'=>'world'));

        $expected = <<<EOT
{
	"foo": "bar",
	"hello": "world"
}
EOT;

        $actual = Utils::JsonPrettyPrint($json);

        $this->assertSame(
            $expected,
            $actual
        );
    }

    /**
     * @covers MarketMeSuite\Phranken\Util\Utils::JsonPrettyPrint
     */
    public function testJsonPrettyPrint2()
    {
        $json = json_encode(null);

        $actual = Utils::JsonPrettyPrint($json);

        $this->assertSame(
            'null',
            $actual
        );
    }
}
