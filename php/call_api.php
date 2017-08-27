<?php
class EbayAPIQuery {

	// required parameters
	public $appID;
	public $operationName;
	public $query;

	// optional parameters
	public $endpoint = 'http://svcs.ebay.com/services/search/FindingService/v1';
	public $version = '1.0.0';
	public $globalID = 'EBAY-US';

	// tracking variables
	public $pageNumber = 1;
	public $callHistory = array();

	public function __construct($appID, $operationName, $query,$optionals = array()) {

		// required parameters
		$this->appID = $appID;
		$this->query = $query;
		$this->operationName = $operationName;

		// optional parameters
		if(isset($optionals['endpoint'])) {
			$this->endpoint = $optionals['endpoint'];
		}
		if(isset($optionals['version'])) {
			$this->version = $optionals['version'];
		}
		if(isset($optionals['globalID'])) {
			$this->globalID = $optionals['globalID'];
		}

	}

	public function makeCall() {
		$queryURL = urlencode($this->query);
		$request = "$this->endpoint?";
		$request .= "OPERATION-NAME=$this->operationName";
		$request .= "&SERVICE-VERSION=$this->version";
		$request .= "&SECURITY-APPNAME=$this->appID";
		$request .= "&GLOBAL-ID=$this->globalID";
		$request .= "&keywords=$queryURL";
		$request .= "&paginationInput.entriesPerPage=100";
		$request .= "&paginationInput.pageNumber=$this->pageNumber";

		if($resp = simplexml_load_file($request)) {
			if($resp->ack == 'Success') {
				$callHistory[time()] = json_encode($resp);
				return json_encode($resp);
			}
		}
		$callHistory[time()] = false;
		return false;
	}

	public function make100Calls() {
		$calls = array();
		for ($i=1; $i <= 100; $i++) { 
			$this->pageNumber = $i;
			if($thisCall = $this->makeCall()) {
				array_push($calls, $thisCall);
			} else {
				return $calls;
			}
		}
		$this->pageNumber = 1;
		$fp = fopen('../sweeps/foo.txt', 'w');
		fwrite($fp, json_encode($calls));
		return $calls;
	}

}

$appid = 'PatrickF-FirstTry-PRD-eb7edec1b-d2b445b5';

$q = new EbayAPIQuery($appid, 'findCompletedItems', 'harry potter');
$q = new EbayAPIQuery($appid, 'findItemsByKeywords', 'harry potter');

//echo $q->make100Calls();

$q->make100Calls();

?>





