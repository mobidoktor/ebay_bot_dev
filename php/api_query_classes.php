<?php

  ///////////////////////
 // API QUERY CLASSES //
///////////////////////


///////////////////////////
// Abstract API Query class

abstract class EbayApiQuery {

	// required parameters
	private $appID;
	//private $operationName;
	
	// optional parameters with set defaults
	protected $endpoint = 'http://svcs.ebay.com/services/search/FindingService/v1';
	protected $version = '1.0.0';
	protected $globalID = 'EBAY-US';

	abstract public function makeRequest($mainSearch, $opName, $optionals = array());

	// Basic HTTP request with error handling
	protected function sendHTTPRequest($req) {
		if($resp = simplexml_load_file($req)) {
			if($resp->ack == 'Success') {
				return $resp;
			}
		}
		return false;	
	}

}

//////////////////////////////////
// Class for searching by keywords

class EbayApiQueryKeywords extends EbayApiQuery {

	// Init appId
	public function __construct($appID) {
		$this->appID = $appID;
	}

	// makes a single HTTP request to the API and returns results as a ? FINISH
	public function makeRequest($keywords, $operationName = 'findItemsByKeywords', $optionals = array()) {
		
		// Global product variables
		$request = "$this->endpoint?";
		if($operationName === 'completed') {
			$operationName = 'findCompletedItems';
		}
		$request .= "OPERATION-NAME=$operationName";
		$request .= "&SERVICE-VERSION=$this->version";
		$request .= "&SECURITY-APPNAME=$this->appID";
		$request .= "&GLOBAL-ID=$this->globalID";

		// Product specific variables
		$query = urlencode($keywords);
		$request .= "&keywords=$query";

		// Pagination variables
		$request .= "&paginationInput.entriesPerPage=100";
		$request .= "&paginationInput.pageNumber=1";

		return $this->sendHTTPRequest($request);
	}

}

///////////////////////////////////
// Class for searching by productId

class EbayApiQueryProduct extends EbayApiQuery {

	// Init appId
	public function __construct($appID) {
		$this->appID = $appID;
	}

	// makes a single HTTP request to the API and returns results as a ? FINISH
	public function makeRequest($productId, $operationName = 'findItemsByProduct', $optionals = array()) {

		// Global product variables
		$request = "$this->endpoint?";
		if($operationName === 'completed') {
			$operationName = 'findCompletedItems';
		}
		$request .= "OPERATION-NAME=$operationName";
		$request .= "&SERVICE-VERSION=$this->version";
		$request .= "&SECURITY-APPNAME=$this->appID";
		$request .= "&GLOBAL-ID=$this->globalID";

		// Product specific variables
		$request .= "&productId.@type=ReferenceID";
		$request .= "&productId=$productId";

		// Pagination variables
		$request .= "&paginationInput.entriesPerPage=100";
		$request .= "&paginationInput.pageNumber=1";

		return $this->sendHTTPRequest($request);
	}

}

///////////////////////////////////
// Class for searching by categoryId

class EbayApiQueryCategory extends EbayApiQuery {

	// Init appId
	public function __construct($appID) {
		$this->appID = $appID;
	}

	// makes a single HTTP request to the API and returns results as a ? FINISH
	public function makeRequest($categoryId, $operationName = 'findItemsByCategory', $optionals = array()) {

		// Global product variables
		$request = "$this->endpoint?";
		if($operationName === 'completed') {
			$operationName = 'findCompletedItems';
		}
		$request .= "OPERATION-NAME=$operationName";
		$request .= "&SERVICE-VERSION=$this->version";
		$request .= "&SECURITY-APPNAME=$this->appID";
		$request .= "&GLOBAL-ID=$this->globalID";

		// Product specific variables
		$request .= "&categoryId=$categoryId";

		// Pagination variables
		$request .= "&paginationInput.entriesPerPage=100";
		$request .= "&paginationInput.pageNumber=1";

		return $this->sendHTTPRequest($request);
	}
}


  /////////////////////////
 // API QUERY FUNCTIONS //
/////////////////////////

// Gets the product ids of a keyword search query

function getProductIds($response) {
	$resultSet = array();
	$data = $response->searchResult;

	$numProds = count($data->item);

	for($i=0; $i<$numProds; $i++) {
		$thisProd = $data->item[$i];
		if(isset($thisProd->productId)) {
			$thisId = $thisProd->productId->__toString();
			$resultSet[$thisId] = true;
		}
	}
	return $resultSet;
}

// Gets the category ids of a keyword search query

function getCategoryIds($response) {
	$resultSet = array();
	$data = $response->searchResult;

	$numProds = count($data->item);

	for($i=0; $i<$numProds; $i++) {
		$thisProd = $data->item[$i];

		if(isset($thisProd->primaryCategory, $thisProd->primaryCategory->categoryId)) {
			$thisId = $thisProd->primaryCategory->categoryId->__toString();
			$resultSet[$thisId] = $thisProd->primaryCategory->categoryName->__toString();
		}
	}
	return $resultSet;
}







  /////////////////
 // DEV TESTING //
/////////////////


$appid = 'PatrickF-FirstTry-PRD-eb7edec1b-d2b445b5';

$q = new EbayApiQueryKeywords($appid);
echo json_encode(getCategoryIds($q->makeRequest('london irish')));
//echo json_encode($q->makeRequest('macbook pro 2011 i7'));


$catt = new EbayApiQueryCategory($appid);
//echo json_encode(getProductIds($catt->makeRequest('111422')));
echo json_encode($catt->makeRequest('171243'));


//$cat = new EbayApiQueryProduct($appid);
//echo json_encode($cat->makeRequest(110909849));

?>