<?php

class cryptsy {
	protected $apiUrl;
	protected $apiKey;
	protected $apiSecret;
 
	function __construct()
	{
		$this->apiUrl 		= 'https://api.cryptsy.com/api';
		$this->apiKey 		= 'YOUR_CRYPTSY_KEY';
		$this->apiSecret 	= 'YOUR_CRYPTSY_SECRET';
	}

	protected function query($url='',$headers='',$post_data='') {
 
        // our curl handle (initialize if required)
        static $ch = null;
        if (is_null($ch)) {
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; Cryptsy API PHP client; '.php_uname('s').'; PHP/'.phpversion().')');
        }
        if(empty($url)) $url = $this->apiUrl;
        curl_setopt($ch, CURLOPT_URL, $url);
        if (!empty($post_data)) curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        if (!empty($headers)) curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
 
        // run the query
        $res = curl_exec($ch);
 
        if ($res === false) throw new Exception('Could not get reply: '.curl_error($ch));
        $dec = json_decode($res);
        if (!$dec) throw new Exception('Invalid data received, please make sure connection is working and requested API exists');
        return $dec;
	}

	private function get($method, array $req = array()) {
		$req['method'] = $method;
        $mt = explode(' ', microtime());
        $req['nonce'] = $mt[1];
       
        // generate the POST data string
        $post_data = http_build_query($req, '', '&');
 
        $sign = hash_hmac("sha512", $post_data, $this->apiSecret);
 
        // generate the extra headers
        $headers = array(
                'Sign: '.$sign,
                'Key: '.$this->apiKey,
        );

        return $this->query($this->apiUrl,$headers,$post_data);
	}


    private function getinfo($type = 'currency'){
        $values = array();

        $query = $this->get('getinfo');
        if ($query->success) {
            if ($type != 'currency') {
                $values = $query->return->balances_available;
            } else {
                foreach ($query->return->balances_available as $currency => $balance) {
                    $values[] = $currency;
                }
            }
            
        }

        return $values;
    }

    public function getAllMarkets(){
        $values = array();

        $query = $this->get('getmarkets');
        if ($query->success) {
            $values = $query->return;
            
        }

        return $values;
    }

    public function getMarket($marketKey = 'label',$marketLabel='BTC/USD'){
        $values = array();

        $query = $this->get('getmarkets');
        if ($query->success) {
            foreach ($query->return as $market) {
                if ($market->$marketKey == $marketLabel) {
                    $values[] = $market;
                }
            }
            
        }

        return $values;
    }

    public function getCurrency(){
        return $this->getinfo();
    }

    public function getBalance(){
        return $this->getinfo('balance');
    }

    public function getCurrencyValue($marketKey = 'label',$marketLabel='BTC/USD',$valueType='last_trade'){
        $return = '';
        $query = $this->getMarket($marketKey,$marketLabel);
        if (is_array($query) && isset($query[0])) {
            $return = $query[0]->$valueType;
        }
        return $return;
    }

    public function getLatestPrice($marketKey = 'label',$marketLabel='BTC/USD'){
        $return = floatval($this->getCurrencyValue($marketKey,$marketLabel));
        return $return;
    }

    public function getLowestPrice($marketKey = 'label',$marketLabel='BTC/USD'){
        $return = floatval($this->getCurrencyValue($marketKey,$marketLabel,'low_trade'));
        return $return;
    }

    public function getHighestPrice($marketKey = 'label',$marketLabel='BTC/USD'){
        $return = floatval($this->getCurrencyValue($marketKey,$marketLabel,'high_trade'));
        return $return;
    }

    public function getMarketId($marketKey = 'label',$marketLabel='BTC/USD'){
        $return = $this->getCurrencyValue($marketKey,$marketLabel,'marketid');
        return $return;
    }

    public function getLabel($marketKey = 'label',$marketLabel='BTC/USD'){
        $return = $this->getCurrencyValue($marketKey,$marketLabel,'label');
        return $return;
    }

    public function getPrimaryCurrencyCode($marketKey = 'label',$marketLabel='BTC/USD'){
        $return = $this->getCurrencyValue($marketKey,$marketLabel,'primary_currency_code');
        return $return;
    }

    public function getPrimaryCurrencyName($marketKey = 'label',$marketLabel='BTC/USD'){
        $return = $this->getCurrencyValue($marketKey,$marketLabel,'primary_currency_name');
        return $return;
    }

    public function getSecondaryCurrencyCode($marketKey = 'label',$marketLabel='BTC/USD'){
        $return = $this->getCurrencyValue($marketKey,$marketLabel,'secondary_currency_code');
        return $return;
    }

    public function getSecondaryCurrencyName($marketKey = 'label',$marketLabel='BTC/USD'){
        $return = $this->getCurrencyValue($marketKey,$marketLabel,'secondary_currency_name');
        return $return;
    }

    public function getCurrentVolume($marketKey = 'label',$marketLabel='BTC/USD'){
        $return = $this->getCurrencyValue($marketKey,$marketLabel,'current_volume');
        return $return;
    }

    public function getCurrentVolumeInBtc($marketKey = 'label',$marketLabel='BTC/USD'){
        $return = $this->getCurrencyValue($marketKey,$marketLabel,'current_volume_btc');
        return $return;
    }

    public function getCurrentVolumeInUsd($marketKey = 'label',$marketLabel='BTC/USD'){
        $return = $this->getCurrencyValue($marketKey,$marketLabel,'current_volume_usd');
        return $return;
    }


}
