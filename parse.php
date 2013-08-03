<?php

/**
 * Yahoo! JAPAN Web APIのご利用には、アプリケーションIDの登録が必要です。
 * あなたが登録したアプリケーションIDを $appid に設定してお使いください。
 * アプリケーションIDの登録URLは、こちらです↓
 * http://e.developer.yahoo.co.jp/webservices/register_application
 */
$appid = ''; // <-- ここにあなたのアプリケーションIDを設定してください。
$concat_count_max = 10; //区切る文節数

function escapestring($str) {
	return htmlspecialchars($str, ENT_QUOTES);
}

function is_alphabet($text) {
	if (preg_match("/^[a-zA-Z]+$/",$text)) {
		return TRUE;
	} else {
		return FALSE;
	}
}

if (isset($_POST['value'])) {
	$sentence = mb_convert_encoding($_POST['value'], 'utf-8', 'auto');
	$sentence = mb_convert_kana($sentence, "KVa");
}
else {
	$sentence = "";
}
header("Content-Type: text/plain; charset=utf-8");
if ($sentence != "") {
	$url = "http://jlp.yahooapis.jp/MAService/V1/parse?appid=".$appid."&results=ma&filter=".urlencode("1|2|3|4|5|6|7|8|9|10|11|12");
	$url .= "&sentence=".urlencode($sentence);
	$xml  = simplexml_load_file($url);
	$before_is_ja = TRUE;
	$concat_count = 0;
	foreach ($xml->ma_result->word_list->word as $cur){
			$is_ja = is_alphabet($cur->surface) ? FALSE : TRUE;
			$concat = $before_is_ja == $is_ja ? "" : ",";
			$concat_count = $before_is_ja == $is_ja ? $concat_count + 1 : 0;
			if($concat_count > $concat_count_max){
				$concat = $concat.",";
				$concat_count = 0;
			}
			echo $concat.$cur->surface;
			$before_is_ja = $is_ja;
		//echo $cur->surface.",";
	}
}