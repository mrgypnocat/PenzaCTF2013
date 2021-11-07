<?php	
	error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING); //убираем варнинги, пускай кулхацкеры поебут позги
	$name = strip_data($_GET['command_name']);		//эта шняга делает хотя бы минимальную фильтрацию говна, которое вводит юзер. Функция внизу, суперсложную систему защиты на уровне скрипта делать влом, разрулю средствами СУБД
	
	$info = strip_data($_GET['command_info']);
	$email = strip_data($_GET['command_email']);
	if (!$name){
	echo 'Не введено имя';
	}
	elseif(!$info){
	echo 'Нет описания';
	}
	elseif(!$email){
	echo 'Не указан почтовый адрес';
	}
	else{
	$db_connect = mysql_connect('localhost','ctrlpnz','0b9f8e73c5c7');
	mysql_select_db('ctrlpnz',$db_connect);	

	mysql_query("SET CHARACTER SET `utf8`")or die(mysql_error());
	mysql_query("SET NAMES `utf8`")or die(mysql_error());
	mysql_query ("SET character_set_client=`utf8`");
	mysql_query ("SET character_set_results=`utf8`");
	mysql_query ("SET collation_connection=`utf8`");


	mysql_query("COLLATE `utf8`", $db_connect);
	$query ="SELECT `name` FROM  `participants` WHERE name={$name} LIMIT 1";
	$sql = mysql_query($query);	
	if(mysql_num_rows($sql) > 0){
		echo '</br>Увы, это имя занято!</br>';
	}
	else{
		$query = "INSERT INTO `participants`
				   (`name`,`info`,`mail`) 
				   VALUES
				   (
				   '{$name}', 
				   '{$info}',
				   '{$email}'
				   )";
		 mysql_query($query) or die(mysql_error());		   
		//mail("maestro-first@yandex.ru", {$name}, "Line 1\nLine 2\nLine 3"); 
		
		echo '</br>Вы восхитительны!</br>';
		echo '</br>Название команды: ',$name,'</br> Описание команды: ',$info,'</br>Почтовый адрес: ',$email;
		echo '</br></br>Ваша заявка на регистрацию поступила, ждите подтверждения регистрации ';	
		}
	}
	echo '</br><a href="/ctf/?page=4">Вернуться на сайт</a>';
	//echo '<meta http-equiv="refresh" content="0;URL=/myredirect.php">';
	
	
	
	function strip_data($text){
	//типа мини-фильтр вводимого говна
    $quotes = array ("\x27", "\x22", "\x60", "\t", "\n", "\r", "*", "%", "<", ">", "?", "!", "'" );
    $goodquotes = array ("-", "+", "#" );
    $repquotes = array ("\-", "\+", "\#" );
    $text = trim( strip_tags( $text ) );
	
	$text = htmlspecialchars($text);
	$text = mysql_escape_string($text);
	
    $text = str_replace( $quotes, ' nubo_hacker', $text );
    $text = str_replace( $goodquotes, $repquotes, $text );
    $text = preg_replace('#[ +]#', ' ', $text);

    return $text;
}
?>