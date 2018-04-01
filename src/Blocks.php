<?php

/**
 * PHP version 7.1
 *
 * @package AM\Blockinfo
 */

namespace AM\Blockinfo;

use \Exception;
use \Logics\Foundation\HTTP\HTTPclient;

/**
 * Class for work with poloniex API
 *
 * @author  Andrey Mashukov <a.mashukoff@gmail.com>
 */

class Blocks
    {

	/**
	 * Get information about new block
	 *
	 * @param string $currency Name of currency
	 *
	 * @return array Block information
	 */

	public function getNew(string $currency):array
	    {
		$resources = [
		    "bitcoin"      => "blockchair",
		    "bitcoin-cash" => "blockchair",
		];

		if (isset($resources[$currency]) === true)
		    {
			switch ($resources[$currency])
			    {
				case "blockchair":
				    return $this->_getFromBlockchair($currency);
			    } //end switch

		    }
		else
		    {
			return $this->_getFromWhattomine($currency);
		    } //end if

	    } //end getNew()


	/**
	 * Get block data from blockchair.com
	 *
	 * @param string $currency Name of cryptocurrency
	 *
	 * @return array or throw exception
	 *
	 * @throws Exception Blockchair.com URL was not defined
	 *
	 * @exceptioncode EXCEPTION_BLOCKCHAIR_COM_URL_WAS_NOT_DEFINED
	 */

	private function _getFromBlockchair(string $currency):array
	    {
		if (defined("BLOCKCHAIR_URL") === true)
		    {
			$url  = BLOCKCHAIR_URL . "/" . $currency . "/blocks";
			$json = $this->_makeRequest($url);

			return $json["data"][0];
		    }
		else
		    {
			throw new Exception("Blockchair.com URL was not defined", EXCEPTION_BLOCKCHAIR_COM_URL_WAS_NOT_DEFINED);
		    } //end if

	    } //end _getFromBlockchair()


	/**
	 * Get block data from whattomine.com
	 *
	 * @param string $currency Name of cryptocurrency
	 *
	 * @return array or throw exception
	 *
	 * @throws Exception Whattomine.com URL was not defined
	 *
	 * @exceptioncode EXCEPTION_WHATTOMINE_COM_URL_WAS_NOT_DEFINED
	 */

	private function _getFromWhattomine(string $currency):array
	    {
		if (defined("WHATTOMINE_URL") === true)
		    {
			$urls = [
			    WHATTOMINE_URL . "/coins.json",
			    WHATTOMINE_URL . "/asic.json",
			];

			$data = [];
			foreach ($urls as $url)
			    {
				$result = $this->_makeRequest($url);
				$data = array_merge($data, array_change_key_case($result["coins"], CASE_UPPER));
			    } //end foreach

			return $this->_convertKeys($data[strtoupper($currency)]);
		    }
		else
		    {
			throw new Exception("Whattomine.com URL was not defined", EXCEPTION_WHATTOMINE_COM_URL_WAS_NOT_DEFINED);
		    } //end if

	    } //end _getFromBlockchair()


	/**
	 * Convert array keys
	 *
	 * @param array $input Array to convert
	 *
	 * @return array Converted array
	 */

	private function _convertKeys(array $input):array
	    {
		$convert = [
		    "last_block"   => "id",
		    "block_reward" => "reward",
		];

		foreach ($convert as $from => $to)
		    {
			$input[$to] = $input[$from];
			unset($input[$from]);
		    } //end foerach

		return $input;
	    } //end _convertKeys()


	/**
	 * Make request
	 *
	 * @param string $url URL to get
	 *
	 * @return array Decoded json
	 */

	private function _makeRequest(string $url):array
	    {
		$http = new HTTPclient($url);
		return json_decode($http->get(), true);
	    } //end _makeRequest()


    } //end class


?>
