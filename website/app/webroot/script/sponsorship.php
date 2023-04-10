<?php
		
$hash_key = 'ssponsor';

$data = 'bWlmlZs%3C';


$maCleDeCryptage = md5($hash_key);
		$letter = -1;
		$newstr = '';
$maChaineCrypter = base64_decode(str_pad(strtr(urldecode($data), '-|', '+_'), strlen($data) % 4, '=', STR_PAD_RIGHT)); 
		$strlen = strlen($maChaineCrypter);
		for ( $i = 0; $i < $strlen; $i++ ){
			$letter++;
			if ( $letter > 31 ){
				$letter = 0;
			}
			$neword = ord($maChaineCrypter{$i}) - ord($maCleDeCryptage{$letter});
			if ( $neword < 1 ){
				$neword += 256;
			}
			$newstr .= chr($neword);
		}
var_dump($newstr);
?>