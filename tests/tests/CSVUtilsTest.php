<?php
namespace MarketMeSuite\Phranken\Util;

class CSVUtilsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers MarketMeSuite\Phranken\Util\CSVUtils::readCSV
     */
    public function testReadCSV()
    {
        // test map parsing
        $actual   = CSVUtils::readCSV('test.csv', array(0 => 'key', 1 => 'value'));
        $expected = array(
            0 =>
                array(
                    'key'   => 'Keys',
                    'value' => 'Values',
                ),
            1 =>
                array(
                    'key'   => 'foo',
                    'value' => 'bar',
                ),
            2 =>
                array(
                    'key'   => 'jim',
                    'value' => 'jack',
                ),
            3 =>
                array(
                    'key'   => 'monkey',
                    'value' => 'fun',
                ),
        );

        $this->assertEquals($expected, $actual);

        // test dataFunction
        $actual   = CSVUtils::readCSV(
            'test.csv',
            array(),
            function ($row, $data, &$items) {
                if ($data[0] === 'Keys') {
                    return;
                }

                $items[] = array('key' => $data[0], 'value' => $data[1]);
            }
        );

        $expected = array(
            0 =>
                array(
                    'key'   => 'foo',
                    'value' => 'bar',
                ),
            1 =>
                array(
                    'key'   => 'jim',
                    'value' => 'jack',
                ),
            2 =>
                array(
                    'key'   => 'monkey',
                    'value' => 'fun',
                ),
        );

        $this->assertEquals($expected, $actual);

        // test skip rows
        $actual   = CSVUtils::readCSV('test.csv', array(0 => 'key', 1 => 'value'), null, array(1));

        $expected = array(
            0 =>
                array(
                    'key'   => 'foo',
                    'value' => 'bar',
                ),
            1 =>
                array(
                    'key'   => 'jim',
                    'value' => 'jack',
                ),
            2 =>
                array(
                    'key'   => 'monkey',
                    'value' => 'fun',
                ),
        );

        $this->assertEquals($expected, $actual);

        // test skip rows multiple
        $actual = CSVUtils::readCSV('test.csv', array(0 => 'key', 1 => 'value'), null, array(1, 2, 4));

        $expected = array(
            0 =>
                array(
                    'key'   => 'jim',
                    'value' => 'jack',
                )
        );

        $this->assertEquals($expected, $actual);

        // missing file
        $actual = CSVUtils::readCSV('this_file_does_not_exist.csv', array());
        $expected = false;

        $this->assertEquals($expected, $actual);
    }

    /**
     * @covers MarketMeSuite\Phranken\Util\CSVUtils::createCSVFromRows
     */
    public function testCreateCSVFromRows()
    {
        $rows = array(
            array('foo', 'bar'),
            array('monkey', 'fun'),
            array('homer', 'simpson'),
            array('purple', 'monkey'),
        );

        $actual = CSVUtils::createCSVFromRows($rows);
        $expected = <<<EOT
foo,bar
monkey,fun
homer,simpson
purple,monkey

EOT;

        $this->assertSame($expected, $actual);
    }
}
