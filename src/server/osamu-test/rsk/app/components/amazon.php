<?php
App::import('Core', array('Xml', 'HttpSocket'));

class AmazonComponent extends Object{
	public $name = 'Amazon';
	var $params = array(
		'Service' => 'AWSECommerceService',	
		'AWSAccessKeyId' => 'AKIAJGUVQ4L3GZVT5YPA',  //アクセスキー
		'AssociateTag' => 'n0bisuke-22'   //アソシエイトのタグ
	);
	var $sKey = '9U7Chnxrc89uL+xy8r3/+HBoaxNQKjvVtdVsC00h';  //シークレットキー（最近導入されたアレ）
	var $baseUrl = 'http://ecs.amazonaws.jp/onca/xml'; //comでもjpでも好きにしたらいいさ
	/**
	 * signature作成
	 */
	function _signature($params){
		$params['Timestamp'] = gmdate('Y-m-d\TH:i:s\Z');
		ksort($params);
		$str = '';
		foreach($params as $key => $val){
			$str .= '&'.$this->_rfc3986($key).'='.$this->_rfc3986($val);
		}
		$str = substr($str,1);
		$url = parse_url($this->baseUrl);
		$signatureStr = "GET\n{$url['host']}\n{$url['path']}\n{$str}";
		$signature = base64_encode(hash_hmac('sha256', $signatureStr, $this->sKey, true));
		$params['Signature'] = $this->_rfc3986($signature);

		return $str.'&Signature='.$this->_rfc3986($signature);
		//return $params;
		
	}
	/**
	 * RFC3986形式
	 */
	function _rfc3986($str) {
		 return str_replace('%7E', '~', rawurlencode($str));
	} 
	/**
	 * データ取得
	 */
	function searchIndex($pr = array()) {
		$options = array(
			'Operation' => 'ItemSearch',
		);
		$params = array_merge($this->params, $options);
		$params = array_merge($params, $pr);
		$params = $this->_signature($params);
		
		$socket = new HttpSocket();
		$header = aa('header', aa('User-Agent', 'amazon'));
		$response = $socket->get($this->baseUrl, $params, $header);
		$result = Set::reverse(new Xml($response));

		if (isset($result["ItemSearchResponse"]['Items']['Request']['Errors'])) {
			foreach ($result["ItemSearchResponse"]['Items']['Request']['Errors'] as $error) {
				$this->__lastErrors['itemSearch'][] = $error;
			}
			return false;
		}
		
		return $result;
	}
}
?>