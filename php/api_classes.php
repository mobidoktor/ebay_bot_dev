<?php

// Abstract API Query class

abstract class EbayApiQuery {

	// required parameters
	private $appID;
	private $operationName;
	
	// optional parameters with set defaults
	protected $endpoint = 'http://svcs.ebay.com/services/search/FindingService/v1';
	protected $version = '1.0.0';
	protected $globalID = 'EBAY-US';

	abstract public function makeRequest($mainSearch, $optionals = array());

}

// Class for searching by keywords

class EbayApiQueryKeywords extends EbayApiQuery {

	// constructor function
	public function __construct($appID) {
		$this->appID = $appID;
		$this->operationName = 'findItemsByKeywords';
	}

	// makes a single HTTP request to the API and returns results as a ? FINISH
	public function makeRequest($keywords, $optionals = array()) {
		$query = urlencode($keywords);

		$request = "$this->endpoint?";
		$request .= "OPERATION-NAME=$this->operationName";
		$request .= "&SERVICE-VERSION=$this->version";
		$request .= "&SECURITY-APPNAME=$this->appID";
		$request .= "&GLOBAL-ID=$this->globalID";
		$request .= "&keywords=$query";
		$request .= "&paginationInput.entriesPerPage=100";
		$request .= "&paginationInput.pageNumber=1";

		if($resp = simplexml_load_file($request)) {
			if($resp->ack == 'Success') {
				return $resp;
			}
		}
		return false;
	}

}

// Class for searching by productId

class EbayApiQueryProduct extends EbayApiQuery {

	// constructor function
	public function __construct($appID) {
		$this->appID = $appID;
		$this->operationName = 'findItemsByProduct';
	}

	// makes a single HTTP request to the API and returns results as a ? FINISH
	public function makeRequest($productId, $optionals = array()) {

		// global request stuff
		$request = "$this->endpoint?";
		$request .= "OPERATION-NAME=$this->operationName";
		$request .= "&SERVICE-VERSION=$this->version";
		$request .= "&SECURITY-APPNAME=$this->appID";
		$request .= "&GLOBAL-ID=$this->globalID";

		// query stuff
		$request .= "&productId.@type=ReferenceID";
		$request .= "&productId=$productId";

		// pagination stuff
		$request .= "&paginationInput.entriesPerPage=100";
		$request .= "&paginationInput.pageNumber=1";

		if($resp = simplexml_load_file($request)) {
			if($resp->ack == 'Success') {
				//return $request;
				return $resp;
			}
		}
		return false;
	}

}

// Class for searching completed listings by keywords

class EbayApiQueryCompletedKeywords extends EbayApiQuery {

	// constructor function
	public function __construct($appID) {
		$this->appID = $appID;
		$this->operationName = 'findCompletedItems';
	}

	// makes a single HTTP request to the API and returns results as a ? FINISH
	public function makeRequest($mainSearch, $optionals = array()) {
		return false;
	}

}

// Class for searching completed listings by productId

class EbayApiQueryCompletedProduct extends EbayApiQuery {

	// constructor function
	public function __construct($appID) {
		$this->appID = $appID;
		$this->operationName = 'findCompletedItems';
	}

	// makes a single HTTP request to the API and returns results as a ? FINISH
	public function makeRequest($mainSearch, $optionals = array()) {
		return false;
	}

}


$appid = 'PatrickF-FirstTry-PRD-eb7edec1b-d2b445b5';

$q = new EbayApiQueryKeywords($appid);
//echo json_encode($q->makeRequest('macbook pro 2011 i7'));


$cat = new EbayApiQueryProduct($appid);
echo json_encode($cat->makeRequest(110909849));

?>