<?php
function convertJson($string)
{
	$find =    array("á", "é", "í", "ó", "ú", "Á", "É", "Í", "Ó", "Ú", "ñ", "Ñ");
	$replace = array("&aacute;", "&eacute;", "&iacute;", "&oacute;", "&uacute;", "&Aacute;", "&Eacute;", "&Iacute;", "&Oacute;", "&Uacute;", "&ntilde;", "&Ntilde;");
	$finalString = str_replace($find, $replace, $string);

	return mb_convert_encoding($finalString, 'UTF-8', 'ISO-8859-1');
}
?>