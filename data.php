<?php
//прочитать 2 строки из файла (надо переделать)
function fo($contr) {
	if (!file_exists($contr)) { $handle = fopen($contr, "w"); fclose($handle); }
	$handle = fopen($contr, "r");
	$b1 = '';
   $b1 = fgets($handle);
	fclose($handle);
	return $b1;
}
//записать в файл (надо переделать)
function fs($contr, $id) {
	$handle = fopen($contr, "w");
	$b = fwrite($handle, $id);
	fclose($handle);
	return $b;
}
//добавить в конец файла (надо переделать)
function fa($contr, $id) {
	$handle = fopen($contr, "a");
	$b = fwrite($handle, $id);
	fclose($handle);
	return $b;
}
//делатель текстового отображения цепочки
function echocep($cep) {
	$ret = strtoupper($cep);
	$rtrn = $ret{0};
	for($i=1;$i<=strlen($cep)-1;$i++) { $rtrn .= '-'.$ret{$i}; }
	return $rtrn;
}

?>