<?php
class A{
	private static $accessKey = "3xzaa5e7-xxxx";
	private static $secretKey = "b2904f2d-xxxx";
	private static $debug = false;
	
	static public function getAccountInfo(){
		return self::request('getAccountInfo');
	}
	
	//$params = array(100,1);
	static public function buyOrder($params){
		if(is_array($params)){
			return self::request('buyOrder', $params);
		}
		return false;
	}
	
	//$params = array(100,1);
	static public function sellOrder($params){
		if(is_array($params)){
			return self::request('getOrder', $params);
		}
		return false;
	}
	
	//$params = array(2);
	static public function cancelOrder($params){
		if(is_array($params)){
			return self::request('cancelOrder', $params);
		}
		return false;
	}
	
	//$params = array(2);
	static public function getOrder($params){
		if(is_array($params)){
			return self::request('getOrder', $params);
		}
		return false;
	}
	
	static public function getOrders(){
		return self::request('getOrders');
	}
	
	//all | fundbtc | withdrawbtc | fundmoney | withdrawmoney | refundmoney | buybtc | sellbtc | tradefee
	//$params = array(type, limit);
	static public function getTransactions($params){
		return self::request('getTransactions', $params);
	}
	
	//$params = array('BTC', [TRUE|FALSE]);
	static public function getDeposits($params){
		return self::request('getDeposits', $params);
	}
	
	//$params = array(10);
	static public function getMarketDepth2($params = array()){
		return self::request('getMarketDepth2', $params);
	}
	
	//$params = array(2);
	static public function getWithdrawal($params){
		if(is_array($params)){
			return self::request('getWithdrawal', $params);
		}
		return false;
	}
	
	//$params = array('BTC', [TRUE|FALSE]);
	static public function getWithdrawals($params){
		if(is_array($params)){
			return self::request('getWithdrawals', $params);
		}
		return false;
	}
	
	//BTC | CNY
	//$params = array('BTC', 1);
	static public function requestWithdrawal($params){
		if(is_array($params)){
			return self::request('requestWithdrawal', $params);
		}
		return false;
	}
	
	function sign($method, $params = array()){

		$mt = explode(' ', microtime());
		$ts = $mt[1] . substr($mt[0], 2, 6);

		$signature = http_build_query(array(
			'tonce' => $ts,
			'accesskey' => self::$accessKey,
			'requestmethod' => 'post',
			'id' => 1,
			'method' => $method,
			'params' => '', //implode(',', $params),
		));

		if(self::$debug){
			var_dump($signature);
		}

		$hash = hash_hmac('sha1', $signature, self::$secretKey);

		return array(
			'ts' => $ts,
			'hash' => $hash,
			'auth' => base64_encode(self::$accessKey.':'. $hash),
		);
	}

	function request($method, $params=array()){
		$sign = self::sign($method, $params);

		$options = array( 
			CURLOPT_HTTPHEADER => array(
				'Authorization: Basic ' . $sign['auth'],
				'Json-Rpc-Tonce: ' . $sign['ts'],
			),
		);

		$postData = json_encode(array(
			'method' => $method,
			'params' => $params,
			'id' => 1,
		));
		
		if(self::$debug){
			print($postData);
		}
		
		$headers = array(
				'Authorization: Basic ' . $sign['auth'],
				'Json-Rpc-Tonce: ' . $sign['ts'],
			);        
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_USERAGENT, 
				'Mozilla/4.0 (compatible; BTC China Trade Bot; '.php_uname('a').'; PHP/'.phpversion().')'
				);

		curl_setopt($ch, CURLOPT_URL, 'https://api.btcchina.com/api_trade_v1.php');
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

		// run the query
		$res = curl_exec($ch);
		return $res;
		/**/
	}
}