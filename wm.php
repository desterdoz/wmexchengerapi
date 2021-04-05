<?php
include_once('data.php');

//отправка запроса на биржу через курл
	function file_get_contents_curl($purl,$wmwm,$context,$pcrt,$wmPass) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$purl1 = 'https://wm.exchanger.ru/asp/XMLWMList.asp?exchtype=';
		if($purl == $purl1){
			$purl = $purl.$wmwm;
			curl_setopt($ch, CURLOPT_POST, 0);
			curl_setopt($ch, CURLOPT_URL, $purl);
		}
		else {
			curl_setopt($ch, CURLOPT_SSLVERSION,1); 
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_URL, $purl);
			curl_setopt($ch, CURLOPT_SSLCERT, $pcrt);
			curl_setopt($ch, CURLOPT_SSLCERTPASSWD, $wmPass);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $context);
		}
		$ret = curl_exec($ch);
		if ($ret === FALSE) {  
    		//Тут-то мы о ней и скажем  
   		$ret = "cURL Error: " . curl_error($ch);
		}  		
		curl_close($ch);
		//$vsp = fa("xml.log", date("d.m.Y H:i:s", time())." ".$purl." - ".$context."\r\n".$ret."\r\n");
		return $ret;
	}
//запрос получения списка заявок по одной паре и распарсивание ответа в массив ()
	function get_list($wmwm) {
		$purl1='https://wm.exchanger.ru/asp/XMLWMList.asp?exchtype=';
		$xml = file_get_contents_curl($purl1,$wmwm,'','','');
		if(!$xml) return false;
		$xml = simplexml_load_string($xml);
		if(!$xml) return false;
		$ret = array();
		foreach($xml->WMExchnagerQuerys->query as $q) {
			$a = $q->attributes();
			$d = array();
			foreach($a as $n=>$v) {
				$v = strtr($v,",",".");
				$d[strval($n)] = strval($v);
			}
			$ret[] = $d;
		}
		return $ret;
	}
//запрос постановки новой заявки на обмен и распарсивание ответа в массив
	function set_pay($inpurse, $outpurse, $inamount, $outamount, $pcrt, $wmPass) {
		$rr = ras($inamount, 1);
		$req = '<wm.exchanger.request><inpurse>'.$inpurse.'</inpurse><outpurse>'.$outpurse.'</outpurse><inamount>'.$rr.'</inamount><outamount>'.$outamount.'</outamount></wm.exchanger.request>';
		$result = file_get_contents_curl('https://wmeng.exchanger.ru/asp/XMLTrustPay.asp', 0, $req, $pcrt, $wmPass);
		if(!$result) return false;
		$xml = simplexml_load_string($result);
		if(!$xml) return false;
		$ret = array();
		foreach($xml->retval as $q) {
			$a = $q->attributes();
			$d = array();
			foreach($a as $n=>$v) {
				$d[strval($n)] = strval($v);
			}
			$ret[] = $d;
		}
		return $ret;
	}
//запрос на изменение курса заданной заявки
	function new_kurs($operid, $curstype, $cursamount, $pcrt, $wmPass) {
		$req = '<wm.exchanger.request><operid>'.$operid.'</operid><curstype>'.$curstype.'</curstype><cursamount>'.$cursamount.'</cursamount></wm.exchanger.request>';
		$result = file_get_contents_curl('https://wmeng.exchanger.ru/asp/XMLTransIzm.asp', 0, $req, $pcrt, $wmPass);

		return $result;
	}
//запрос получения заявки по номеру и распарсивание ответа в массив
	function get_z($nz, $tz, $pcrt, $wmPass) {
		$req = '<wm.exchanger.request><type>'.$tz.'</type><queryid>'.$nz.'</queryid></wm.exchanger.request>';
		$result = file_get_contents_curl('https://wmeng.exchanger.ru/asp/XMLWMList2.asp', 0, $req, $pcrt, $wmPass);
//		echo $result;
		if(!$result) return false;
		$xml = simplexml_load_string($result);
		if(!$xml) return false;
		$ret = array();
		foreach($xml->WMExchnagerQuerys->query as $q) {
			$a = $q->attributes();
			$d = array();
			foreach($a as $n=>$v) {
				$v = strtr($v,",",".");
				$d[strval($n)] = strval($v);
			}
			$ret[] = $d;
		}
		return $ret;
	}
//запрос обмена заявки на самую первую встречную и распарсивание ответа в массив
	function vstr_obm($isxtrid, $desttrid, $pcrt, $wmPass) {
		$req = '<wm.exchanger.request><isxtrid>'.$isxtrid.'</isxtrid><desttrid>'.$desttrid.'</desttrid><deststamp></deststamp></wm.exchanger.request>';
		$result = file_get_contents_curl('https://wmeng.exchanger.ru/asp/XMLQrFromTrIns.asp', 0, $req, $pcrt, $wmPass);
		if(!$result) return false;
		$xml = simplexml_load_string($result);
		if(!$xml) return false;
		$ret = array();
		foreach($xml->retval as $q) {
			$a = $q->attributes();
			$d = array();
			foreach($a as $n=>$v) {
				$d[strval($n)] = strval($v);
			}
			$ret[] = $d;
		}
		return $ret;
	}
	function get_zv($nz, $pcrt, $wmPass) {
		$req = '<wm.exchanger.request><queryid>'.$nz.'</queryid></wm.exchanger.request>';
		$result = file_get_contents_curl('https://wmeng.exchanger.ru/asp/XMLWMList3.asp', 0, $req, $pcrt, $wmPass);
		//$vsp = fa("/var/www/u21663/data/data/xml.log", date("d.m.Y H:i:s", time())." - ".serialize($result)."\r\n");
		if(!$result) return false;
		$xml = simplexml_load_string($result);
		if(!$xml) return false;
		$ret = array();
		foreach($xml->WMExchnagerQuerys->query as $q) {
			$a = $q->attributes();
			$d = array();
			foreach($a as $n=>$v) {
				$v = strtr($v,",",".");
				$d[strval($n)] = strval($v);
			}
			$ret[] = $d;
		}
		return $ret;
	}

	function get_zvs($nz, $pcrt, $wmPass) {
		$req = '<wm.exchanger.request><queryid>'.$nz.'</queryid></wm.exchanger.request>';
		$result = file_get_contents_curl('https://wmeng.exchanger.ru/asp/XMLWMList3Det.asp', 0, $req, $pcrt, $wmPass);
		//$vsp = fa("xml.log", date("d.m.Y H:i:s", time())." - ".serialize($result)."\r\n");
		if(!$result) return false;
		$xml = simplexml_load_string($result);
		if(!$xml) return false;
		$ret = array();
		foreach($xml->WMExchnagerQuerys->query as $q) {
			$a = $q->attributes();
			$d = array();
			foreach($a as $n=>$v) {
				$v = strtr($v,",",".");
				$d[strval($n)] = strval($v);
			}
			$ret[] = $d;
		}
		return $ret;
	}
?>
