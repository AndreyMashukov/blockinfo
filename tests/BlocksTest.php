<?php

/**
 * PHP version 7.1
 *
 * @package AM\Blockinfo
 */

namespace Tests;

use \PHPUnit\Framework\TestCase;
use \Logics\Tests\InternalWebServer;
use \AM\Blockinfo\Blocks;
use \Exception;

/**
 * Tests for API blockchair get blocks
 *
 * @author  Andrey Mashukov <a.mashukoff@gmail.com>
 *
 * @runTestsInSeparateProcesses
 */

class BlocksTest extends TestCase
    {

	use InternalWebServer;

	/**
	 * Name folder which should be removed after tests
	 *
	 * @var string
	 */
	protected $remotepath;

	/**
	 * Testing host
	 *
	 * @var string
	 */
	protected $host;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @return void
	 */

	protected function setUp()
	    {
		$this->remotepath = $this->webserverURL();
		$this->host       = $this->remotepath . "/HTTPResponder.php";
	    } //end setUp()


	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @return void
	 */

	protected function tearDown()
	    {
		unset($this->remotepath);
	    } //end tearDown()


	/**
	 * Should to get information about last block
	 *
	 * @return void
	 */

	public function testShouldToGetInformationAboutLastBlock()
	    {
		define("BLOCKCHAIR_URL", $this->host);
		$blocks = new Blocks();
		$block  = $blocks->getNew("bitcoin");

		$json = json_decode(file_get_contents(__DIR__ . "/mockdata/response.json"), true);
		$this->assertEquals($json["data"][0], $block);
	    } //end testShouldToGetInformationAboutLastBlock()


	/**
	 * Should not allow make request without defined blockchair URL
	 *
	 * @return void
	 *
	 * @exceptioncode EXCEPTION_BLOCKCHAIR_COM_URL_WAS_NOT_DEFINED
	 */

	public function testShouldNotAllowMakeRequestWithoutDefinedBlockchairUrl()
	    {
		define("EXCEPTION_BLOCKCHAIR_COM_URL_WAS_NOT_DEFINED", 1);
		$blocks = new Blocks();
		$this->expectException(Exception::class);
		$this->expectExceptionCode(EXCEPTION_BLOCKCHAIR_COM_URL_WAS_NOT_DEFINED);
		$block = $blocks->getNew("bitcoin");
	    } //end testShouldNotAllowMakeRequestWithoutDefinedBlockchairUrl()


	/**
	 * Should allow to get block data from whattomine
	 *
	 * @return void
	 */

	public function testShouldAllowToGetBlockDataFromWhattomine()
	    {
		define("WHATTOMINE_URL", $this->remotepath . "/mockdata/whattomine");

		$blocks = new Blocks();
		$block  = $blocks->getNew("dash");

		$expected = json_decode(file_get_contents(__DIR__ . "/dashblock.json"), true);
		$this->assertEquals($expected, $block);
	    } //end testShouldAllowToGetBlockDataFromWhattomine()


	/**
	 * Should allow to get block data of any currency from whattomine
	 *
	 * @return void
	 */

	public function testShouldAllowToGetBlockDataOfAnyCurrencyFromWhattomine()
	    {
		define("WHATTOMINE_URL", $this->remotepath . "/mockdata/whattomine");

		$blocks = new Blocks();
		$block  = $blocks->getNew("ethereum");

		$expected = file_get_contents(__DIR__ . "/ethereum.json");
		$this->assertEquals($expected, json_encode($block));
	    } //end testShouldAllowToGetBlockDataOfAnyCurrencyFromWhattomine()


    } //end class


?>
