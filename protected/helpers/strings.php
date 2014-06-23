<?php
/**********************************************************************************************
*                            CMS Open Real Estate
*                              -----------------
*	version				:	1.8.1
*	copyright			:	(c) 2014 Monoray
*	website				:	http://www.monoray.ru/
*	contact us			:	http://www.monoray.ru/contact
*
* This file is part of CMS Open Real Estate
*
* Open Real Estate is free software. This work is licensed under a GNU GPL.
* http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*
* Open Real Estate is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
* Without even the implied warranty of  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
***********************************************************************************************/

function truncateText($text, $numOfWords = 10, $add = ''){
	if($numOfWords){
		$text = strip_tags($text, '<br/>');
		$text = str_replace(array("\r", "\n"), '', $text);

		$lenBefore = strlen($text);
		if($numOfWords){
			if(preg_match("/\s*(\S+\s*){0,$numOfWords}/", $text, $match)){
				$text = trim($match[0]);
			}
			if(strlen($text) != $lenBefore){
				$text .= '.. '.$add;
			}
		}
	}

	return $text;
}

function utf8_substr($str, $from, $len) {
	return preg_replace('#^(?:[\x00-\x7F]|[\xC0-\xFF][\x80-\xBF]+){0,'.$from.'}'.
	'((?:[\x00-\x7F]|[\xC0-\xFF][\x80-\xBF]+){0,'.$len.'}).*#s',
	'$1',$str);
}

function utf8_strlen($s) {
	return preg_match_all('/./u', $s, $tmp);
}

function utf8_ucfirst($string, $e ='utf-8') {
    if (function_exists('mb_strtoupper') && function_exists('mb_substr') && !empty($string)) {
        $string = mb_strtolower($string, $e);
        $upper = mb_strtoupper($string, $e);
            preg_match('#(.)#us', $upper, $matches);
            $string = $matches[1] . mb_substr($string, 1, mb_strlen($string, $e), $e);
    }
    else {
        $string = ucfirst($string);
    }
    return $string;
}

function utf8_strtolower($string, $e ='utf-8') {
	if (function_exists('mb_strtolower')) {
		$string = mb_strtolower($string, $e);
	}
	else {
		$string = strtolower($string);
	}
	return $string;
}

function translit($str, $separator = 'dash', $lowercase = TRUE)
{
    $str = strip_tags($str);

	$foreign_characters = array(
		'/ä|æ|ǽ/' => 'ae',
		'/ö|œ/' => 'oe',
		'/ü/' => 'ue',
		'/Ä/' => 'Ae',
		'/Ü/' => 'Ue',
		'/Ö/' => 'Oe',
		'/À|Á|Â|Ã|Ä|Å|Ǻ|Ā|Ă|Ą|Ǎ|А/' => 'A',
		'/à|á|â|ã|å|ǻ|ā|ă|ą|ǎ|ª|а/' => 'a',
		'/Б/' => 'B',
		'/б/' => 'b',
		'/Ç|Ć|Ĉ|Ċ|Č|Ц/' => 'C',
		'/ç|ć|ĉ|ċ|č|ц/' => 'c',
		'/Ð|Ď|Đ|Д/' => 'D',
		'/ð|ď|đ|д/' => 'd',
		'/È|É|Ê|Ë|Ē|Ĕ|Ė|Ę|Ě|Е|Ё|Э/' => 'E',
		'/è|é|ê|ë|ē|ĕ|ė|ę|ě|е|ё|э/' => 'e',
		'/Ф/' => 'F',
		'/ф/' => 'f',
		'/Ĝ|Ğ|Ġ|Ģ|Г/' => 'G',
		'/ĝ|ğ|ġ|ģ|г/' => 'g',
		'/Ĥ|Ħ|Х/' => 'H',
		'/ĥ|ħ|х/' => 'h',
		'/Ì|Í|Î|Ï|Ĩ|Ī|Ĭ|Ǐ|Į|İ|И/' => 'I',
		'/ì|í|î|ï|ĩ|ī|ĭ|ǐ|į|ı|и/' => 'i',
		'/Ĵ|Й/' => 'J',
		'/ĵ|й/' => 'j',
		'/Ķ|К/' => 'K',
		'/ķ|к/' => 'k',
		'/Ĺ|Ļ|Ľ|Ŀ|Ł|Л/' => 'L',
		'/ĺ|ļ|ľ|ŀ|ł|л/' => 'l',
		'/М/' => 'M',
		'/м/' => 'm',
		'/Ñ|Ń|Ņ|Ň|Н/' => 'N',
		'/ñ|ń|ņ|ň|ŉ|н/' => 'n',
		'/Ò|Ó|Ô|Õ|Ō|Ŏ|Ǒ|Ő|Ơ|Ø|Ǿ|О/' => 'O',
		'/ò|ó|ô|õ|ō|ŏ|ǒ|ő|ơ|ø|ǿ|º|о/' => 'o',
		'/П/' => 'P',
		'/п/' => 'p',
		'/Ŕ|Ŗ|Ř|Р/' => 'R',
		'/ŕ|ŗ|ř|р/' => 'r',
		'/Ś|Ŝ|Ş|Š|С/' => 'S',
		'/ś|ŝ|ş|š|ſ|с/' => 's',
		'/Ţ|Ť|Ŧ|Т/' => 'T',
		'/ţ|ť|ŧ|т/' => 't',
		'/Ù|Ú|Û|Ũ|Ū|Ŭ|Ů|Ű|Ų|Ư|Ǔ|Ǖ|Ǘ|Ǚ|Ǜ|У/' => 'U',
		'/ù|ú|û|ũ|ū|ŭ|ů|ű|ų|ư|ǔ|ǖ|ǘ|ǚ|ǜ|у/' => 'u',
		'/В/' => 'V',
		'/в/' => 'v',
		'/Ý|Ÿ|Ŷ|Ы/' => 'Y',
		'/ý|ÿ|ŷ|ы/' => 'y',
		'/Ŵ/' => 'W',
		'/ŵ/' => 'w',
		'/Ź|Ż|Ž|З/' => 'Z',
		'/ź|ż|ž|з/' => 'z',
		'/Æ|Ǽ/' => 'AE',
		'/ß/'=> 'ss',
		'/Ĳ/' => 'IJ',
		'/ĳ/' => 'ij',
		'/Œ/' => 'OE',
		'/ƒ/' => 'f',
		'/Ч/' => 'Ch',
		'/ч/' => 'ch',
		'/Ю/' => 'Ju',
		'/ю/' => 'ju',
		'/Я/' => 'Ja',
		'/я/' => 'ja',
		'/Ш/' => 'Sh',
		'/ш/' => 'sh',
		'/Щ/' => 'Shch',
		'/щ/' => 'shch',
		'/Ж/' => 'Zh',
		'/ж/' => 'zh',
	);

	$str = preg_replace(array_keys($foreign_characters), array_values($foreign_characters), $str);

	$replace = ($separator == 'dash') ? '-' : '_';

	$trans = array(
		'&\#\d+?;'                => '',
		'&\S+?;'                => '',
		'\s+'                    => $replace,
		'[^a-z0-9\-_]' => '',
		'_+'            => $replace,
		$replace.'+'            => $replace,
		$replace.'$'            => $replace,
		'^'.$replace            => $replace,
		'\.+$'                    => ''
	);

	foreach ($trans as $key => $val) {
		$str = preg_replace("#".$key."#i", $val, $str);
	}

    $str = rtrim($str, $replace);

    if ($lowercase === TRUE)
	{
		if( function_exists('mb_convert_case') )
		{
			$str = mb_convert_case($str, MB_CASE_LOWER, "UTF-8");
		}
		else
		{
			$str = strtolower($str);
		}
	}

	$permitted_uri_chars = 'a-z 0-9~%.:_\-';

	$str = preg_replace('#[^'.$permitted_uri_chars.']#i', '', $str);

	return trim( stripslashes( substr($str, 0, 50) ) );
}
/**
 * Strip a string from the end of a string
 *
 * @param string $str      the input string
 * @param string $remove   OPTIONAL string to remove
 *
 * @return string the modified string
 */
function rstrtrim($str, $remove=null)
{
	$str    = (string)$str;
	$remove = (string)$remove;

	if(empty($remove))
	{
		return rtrim($str);
	}

	$len = strlen($remove);
	$offset = strlen($str)-$len;
	while($offset > 0 && $offset == strpos($str, $remove, $offset))
	{
		$str = substr($str, 0, $offset);
		$offset = strlen($str)-$len;
	}

	return rtrim($str);

} //End of function rstrtrim($str, $remove=null)

function cleanPostData($data){
	$data = trim($data);
	$data = strip_tags($data);
	$data = addslashes($data);
	$data = strtolower($data);
	$data = preg_replace('~[^a-z0-9 \x80-\xFF]~i', "",$data);
	return $data;
}