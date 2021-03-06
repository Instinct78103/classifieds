<?

define( 'DB_NAME', 'classifieds' );
define( 'DB_USER', 'root' );
define( 'DB_PASSWORD', '' );
define( 'DB_HOST', 'localhost' );

define( 'CODES',
[
'[1][1-5][0-8]',
'[2][1-4][1-4]',
'[2][5][1-3]',
'[3][1-2][1-3]',
'[4][1-4][1-6]',
'[5][0-2][0]',
'[6][0][0]'
] );

$friday = new DateTime('friday');
$WeekNum = $friday->format('Y__W');
define('FILENEW', $WeekNum . '.txt');
define('FILEOLD', 'old.txt');

function pre($arr){
	echo '<pre>';
	print_r($arr);
	echo '</pre>';
}
function txt_cleaner($txt){
	//Функция берет файловый массив и возвращает массив без пустых значений
	$fileArr = file($txt);
	$fileArr = array_values(array_filter($fileArr, "trim"));
	return $fileArr;
}
function searching($txt, $pattern){
	//Функция производит поиск по файлу согласно введенной строке
	if(file_exists($txt)){
		$fileArr = file($txt);
		asort($fileArr);
		foreach($fileArr as $key=>$str){
			$fileArr[$key] = trim(iconv("windows-1251", "utf-8", $str));
			if($pattern && preg_match("~" . quotemeta($pattern) . "~ui", $str)){
				if($txt == FILENEW){
					echo '<p class="matches already">' . $str . '</p>';
				}
				else{
					echo '<p class="matches">' . $str . '</p>';
				}
			}
		}
	}
}
function paste_str($txt, $str){
	//Функция вставляет ТОЛЬКО уникальные строки в txt-файл, причем каждая новая строка будет в начале файла
	//quotemeta() - экранирует символы . \ + * ? [ ^ ] ( $ ) в регулярном выражении.
	$counter = 0;
	if(file_exists($txt)){
		//txt_cleaner($txt);
		foreach(txt_cleaner($txt) as $item){
			if(preg_match("~" . quotemeta($str) . "~ui", $item)){
				$counter++;
			}
		}
		if(!$counter){
			$newStr = $str . file_get_contents($txt);
		}
		else{
			$newStr = file_get_contents($txt);
		}
	}
	else{
		$newStr = $str;
	}
	file_put_contents($txt, $newStr, LOCK_EX);
}
function download_file($file) {
    if (file_exists($file)) {
        // сбрасываем буфер вывода PHP, чтобы избежать переполнения памяти выделенной под скрипт
        // если этого не сделать файл будет читаться в память полностью!
        if (ob_get_level()) {
            ob_end_clean();
        }
        // заставляем браузер показать окно сохранения файла
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . basename($file));
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file));
        // читаем файл и отправляем его пользователю
        if ($fd = fopen($file, 'rb')) {
            while (!feof($fd)) {
                print fread($fd, 1024);
            }
            fclose($fd);
        }
        exit;
    }
    else{
        ?>
        Файл не создан<br><br>
        <a href="/">< Вернуться</a>
        <?php
    }
}