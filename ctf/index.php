<?php
	/* глобальная переменная для подключения к БД */
	global $db_connect;
	$db_connect = mysql_connect('localhost','ctrlpnz','0b9f8e73c5c7');
	mysql_select_db('ctrlpnz',$db_connect);	

	mysql_query("SET CHARACTER SET `utf8`")or die(mysql_error());
	mysql_query("SET NAMES `utf8`")or die(mysql_error());
	mysql_query ("SET character_set_client=`utf8`");
	mysql_query ("SET character_set_results=`utf8`");
	mysql_query ("SET collation_connection=`utf8`");


	mysql_query("COLLATE `utf8`", $db_connect);
	?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<link href='http://fonts.googleapis.com/css?family=Arvo' rel='stylesheet' type='text/css'>
<link href="css/horzebra.css" rel="stylesheet" type="text/css" media="screen" />
<link href="css/style.css" rel="stylesheet" type="text/css" media="screen" />
<title>IBST.PSU CTF</title>
<script type="text/javascript" src="/js/jquery-1.8.2.min.js"></script>
<script type="text/javascript" src="/js/utils.js"></script>
</head>
<body>
	<div id="menu-wrapper">
		<div id="menu">
			<ul>
			<?php				
				if(($_GET['page'])&&($_GET['page'] < 10)){
					$page = $_GET['page'];
				}	
				else{
					$page = 1; 
				}
					$array = array(
							'<a href="/ctf/?page=1">Главная</a>',				
							'<a href="/ctf/?page=2">Новости</a>',
							'<a href="/ctf/?page=3">Правила</a>',
							'<a href="/ctf/?page=4">Регистрация</a>',
							'<a href="/ctf/?page=5">Участники</a>',
							'<a href="/ctf/?page=6">Разработчики</a>',
							'<a href="/ctf/?page=7">Публичный скорборд</a>'
							);
					for($s=0; $s<($page-1); $s++){
						echo '<li>'.$array[$s].'</li>';
					}
					echo '<li class="current_page_item">'.$array[($page-1)].'</li>';				
					for($s=$page; $s<count($array); $s++){
						echo '<li>'.$array[$s].'</li>';
					}
			?>					
			</ul>
		</div>
	</div>

<div id="wrapper">
	<div id="header-wrapper">
		<div id="header">
			<div id="logo">
				<h1><a href="/ctf">IBST.PSU CTF</a></h1>
				<p>provided by <a href="http://ibst.pnzgu.ru/index.php/vse-ostalnoe/ctrl-pnz">CTRL-PNZ</a></p>				
			</div>
		</div>
	</div>	

<?PHP
	//для скорборда несколько другой дизайн, поэтому вынесем отдельно
				if ($_GET['page'] == 666)	{		//!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
					echo '
					<div id="table-wrapper">
						
							<script type="text/javascript">
								updatescores();
								setInterval(updatescores, 5000);
							</script>							
							<table border="5" cellpadding="0" cellspacing="1" id="hor-zebra">
								<thead>
									<tr class="header">
										<th>#</th>
										<th>Команда</th>
										<th class ="logo">Лого</th>
										<th class="tasks">Admin</th>
										<th class="tasks">PPC</th>
										<th class="tasks">Stegastic</th>
										<th class="tasks">Crypto</th>
										<th class="tasks">Web</th>
										<th class="tasks">Joy</th>
										<th class="score">Общий счет</th>
									</tr>
								</thead>
								<tbody></tbody>
							</table>
					</div>';		
				}

	
			
	else{
	//для всего остального, при попытке запихать неведомое говно в адресную строку будет мило выводить главную
	echo '
	<div id="page">	
		<div id="page-bgtop">
			<div id="page-bgbtm">				
				<div id="content">';
				
				switch($_GET['page']){						
					case 2:							
						GetBody('news');
						break;
					case 3:							
						GetBody('rules');
						break;	
					case 4:		
						echo 
						'<div class="post"><h2 class="title">Регистрация</h1></br>
						<form action="registration.php" method="GET">						
							</br></br>
							<H3>Название команды:</H3>						
							
							<p class="meta">Это поле обязательно для заполнения. Убедительно просим воздержаться от мата, всяких спецсимволов и прочих завитушек, а то наша хитрая система их отфильтрует и мы так и не увидим ваших попыток зарегистрироваться. Если вы все делаете правильно, но ВНЕЗАПНО ЧТО-ТО ПОШЛО НЕ ТАК, напишите мне на maestro-first@yandex.ru <a href="mailto: maestro-first@yandex.ru">или ткните вот в эту ссылку</a> (у вас откроется почтовик, если имеется на компе) и мы вместе что-нибудь придумаем.</p>
							<textarea rows="3" cols="50" placeholder="Если ты видишь эту надпись, то, скорее всего, у тебя не Google Chrome. Он ее показывает через раз. Надпись, кстати, исчезнет при вводе текста" name="command_name"></textarea>
							</br></br>
							<H3>Описание команды:</H3> 
							
							<p class="meta">Как ни странно, поле тоже обязательное. Здесь вы можете кратко рассказать о себе, например, о своем составе ну или как вариант минимум - откуда вы, с какой кафедры\факультета. Мы должны знать своих героев!</p>
							
							<textarea rows="10" cols="50" placeholder="Если ты видишь эту надпись, то, скорее всего, у тебя не Google Chrome. Он ее показывает через раз. Надпись, кстати, исчезнет при вводе текста" name="command_info">
							</textarea>
							</br></br>
							<H3>Контактный E-mail:</H3>
							<p class="meta">На этот адрес мы вышлем VPN ключи для доступа в нашу сеть. Лучше указать его правильно.</p>
							<input type="text" name="command_email"/>
							
							<input type="submit" value="Я все ввел и проверил!" />
						</form>
						</div>';
						break;
					case 5:			
						echo'
							<table cols="4" border="5" cellpadding="5" cellspacing="1" width="100%" style="word-wrap: break-word;">						 
								<thead>
									<tr class="header">
										<th>#</th>
										<th>Название команды</th>
										<th width="100">Описание</th>
									</tr>
								</thead>
								<tbody>
						';
						$query = "SELECT `name`, `logo`, `info` FROM `participants` WHERE approved ORDER BY `id`";
						$result = mysql_query($query);
						$i = 0;
						while ($row = mysql_fetch_row($result)){
							echo'
								<tr align="center">
									<td>'.++$i.'</td>
									<td>'.$row[0].'</td>
									
									<td width="50%" colspec="20%">'.$row[2].'</td>
								</tr>
							';			
//							<td width="45px">'.$row[1].'</td>
						}
						echo'								
								</tbody>
							</table>
						';
						break;	
					case 6:							
//						GetBody('devteam');
					GetBody('ups');
						break;				
					case 7:							
						GetBody('ups');
						break;					
					default:							
						GetBody('main');
				}
					
					echo '<div style="clear: both;">&nbsp;</div>
				</div>
				<div id="sidebar">					
					<ul>						
						<li>
							<h2><a href="/ctf?page=2">Новости</a></h2>
					<ul>				
					';
				
			$query = "SELECT ntext, date_format(ndate,'%e.%m.%Y %H:%i') as ndate1 FROM news ORDER BY ndate DESC LIMIT 5";
			$result = mysql_query($query);
				if (!mysql_error()) {
					// Цикл, вынимающий строку как массив с числовым индексом
					while ($row = mysql_fetch_row($result)) {
						echo 
						'<li>
							<a href="/ctf?page=2">'. $row[0].'</a>							
						</li>';											
					};
				}
			echo '
					</ul>
						</li>						
					</ul>
						</div>
					<!-- end #sidebar -->
				<div style="clear: both;">&nbsp;</div>
				</div>
			</div>
		</div>
	</div>';	
	}
?>	
	<!-- end #page -->
</div>
<div id="footer">
	<p>Created by <a href="http://ibst.pnzgu.ru/index.php/vse-ostalnoe/ctrl-pnz">CTRL-PNZ</a>. Design by <a href="http://ibst.pnzgu.ru/index.php/kafedra/kontakty-kafedry-3">Mr.Gypnocat</a>.</p>
</div>
<!-- end #footer -->
</body>
</html>

<?PHP
//Other STUFF
function GetBody($name){
	$query = "SELECT ntitle, ftext, date_format(ndate,'%e.%m.%Y %H:%i') as ndate1 FROM {$name} ORDER BY ndate DESC";
	$result = mysql_query($query);	
		if (!mysql_error()) {
		// Цикл, вынимающий строку как массив с числовым индексом
			while ($row = mysql_fetch_row($result)) {
				echo '
				<div class="post">
					<h2 class="title">
						<a href="/ctf">'.$row[0].'</a>
					</h2>
					<p class="meta">'.$row[2].'</p>
					<div class="entry">
						'.$row[1].'
					</div>
				</div>
				';											
			};
		}
}
function cutString($string, $maxlen) {
    $len = (mb_strlen($string) > $maxlen)
        ? mb_strripos(mb_substr($string, 0, $maxlen), ' ')
        : $maxlen
    ;
    $cutStr = mb_substr($string, 0, $len);
    return (mb_strlen($string) > $maxlen)
        ? '' . $cutStr . '...'
        : '' . $cutStr . ''
    ;
}
?>