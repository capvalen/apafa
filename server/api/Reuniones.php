<?php
include('conexion.php');

switch ($_POST['pedir']) {
	case 'crear': crear($datab); break;
	case 'buscar': buscar($datab); break;
	case 'listar': listar($datab); break;
	case 'detalles': detalles($datab); break;
	case 'borrar': borrar($datab); break;
	case 'matricular': matricular($datab); break;
	default: break;
}

function crear($db){
	$reunion = json_decode($_POST['reunion'], true);
	$sql = $db->prepare("INSERT INTO `reunion`(`fecha`, `asunto`, `detalles`, `idGrado`, `seccion`) VALUES (?, ?, ?, ?, ?)");
	if($sql->execute([ $reunion['fecha'], $reunion['asunto'], $reunion['detalles'], $reunion['idGrado'], $reunion['seccion'] ])){
		$idReunion = $db->lastInsertId();
		echo json_encode( array('idReunion' => $idReunion, 'mensaje' => 'ok'));
	}
}

function listar($db){
	$filas = [];
	$sql = $db->prepare("SELECT * FROM `reunion` where activo = 1 order by id desc;");
	if($sql->execute()){
		while($rows = $sql->fetch(PDO::FETCH_ASSOC))
			$filas[] = $rows;
		echo json_encode( array('reuniones' => $filas, 'mensaje' => 'ok'));
	}
}

function buscar($db){
	$filas = [];
	$sql = $db->prepare("SELECT * FROM `reunion` where activo = 1 and asunto like ? and year(fecha) = ? order by id desc;");
	if($sql->execute([ $_POST['texto'] .'%', $_POST['año'] ])){
		while($rows = $sql->fetch(PDO::FETCH_ASSOC))
			$filas[] = $rows;
		echo json_encode( array('reuniones' => $filas, 'mensaje' => 'ok'));
	}
}

function detalles($db){
	$filas = [];
	$sql = $db->prepare("SELECT * FROM `reunion` as r inner join grados g on g.id = r.idGrado where r.id = ? ;");
	if($sql->execute([ $_POST['idReunion'] ])){
		while($rows = $sql->fetch(PDO::FETCH_ASSOC))
			$filas[] = $rows;
		$asistentes=[];
		$sqlAsistentes = $db->prepare("SELECT * FROM `asistencia` as a inner join padre as p on a.idPadre = p.id where idReunion = ? and a.presente=1;");
		$sqlAsistentes-> execute([ $_POST['idReunion']]);
		while($rowsAsistentes = $sqlAsistentes->fetch(PDO::FETCH_ASSOC))
			$asistentes[] = $rowsAsistentes;
		echo json_encode( array('reunion' => $filas[0], 'asistentes' => $asistentes, 'mensaje' => 'ok'));
	}
}

function borrar($db){
	$sql = $db->prepare("UPDATE `asistencia` SET `presente` = '0' WHERE `idPadre` = ?;");
	if($sql->execute([ $_POST['idPadre'] ])){
		echo json_encode( array('mensaje' => 'eliminado ok'));
	}
}

function matricular($db){
	$sqlRepetido = $db->prepare('SELECT * FROM matricula where activo = 1 and idAlumno = ? and idGrado = ? and año = ? and seccion = ?;');
	if($sqlRepetido -> execute([ $_POST['idAlumno'], $_POST['idGrado'], $_POST['año'], $_POST['seccion'] ])){
		$conteo =  $sqlRepetido->rowCount();
		if($conteo==0){
			$sql = $db->prepare("INSERT INTO `matricula`(`idAlumno`, `idGrado`, `año`, `seccion`) VALUES (?, ?, ?, ?)");
			if($sql->execute([ $_POST['idAlumno'], $_POST['idGrado'], $_POST['año'], $_POST['seccion'] ])){
				$idReunion = $db->lastInsertId();
				echo json_encode( array('idReunion' => $idReunion, 'mensaje' => 'ok'));
			}
		}else{
			echo json_encode( array('mensaje' => 'duplicado'));
		}
	}
	
	
}