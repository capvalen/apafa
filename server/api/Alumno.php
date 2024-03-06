<?php
include('conexion.php');

switch ($_POST['pedir']) {
	case 'buscarDNI': buscarDNI($datab); break;
	case 'listar30Alumnos': listar30Alumnos($datab); break;
	case 'buscarAlumno': buscarAlumno($datab); break;
	case 'eliminarAlumno': eliminarAlumno($datab); break;
	default: break;
}

function buscarDNI($db){
	$sql = $db->prepare("SELECT * FROM `alumno` WHERE dni = ? and activo = 1 and dni<>'' limit 1;");
	if($sql->execute([ $_POST['dni'] ])){
		$conteo =  $sql->rowCount();
		$apoderado = [];
		if($conteo>0){
			$rows = $sql->fetch(PDO::FETCH_ASSOC);
			$apoderado = $rows;
		}
		echo json_encode( array('conteo' => $conteo, 'alumno' => $apoderado, 'mensaje' => 'ok'));
	}
}

function listar30Alumnos($db){
	$filas = [];
	$sql = $db->prepare("SELECT * FROM `alumno` WHERE activo = 1 order by id desc limit 30;");
	if($sql->execute()){
		while($rows = $sql->fetch(PDO::FETCH_ASSOC))
			$filas[] = $rows;
		echo json_encode( array('alumnos' => $filas, 'mensaje' => 'ok'));
	}
}

function buscarAlumno($db){
	$filas = [];
	$sql = $db->prepare("SELECT * FROM `alumno` WHERE (dni = '{$_POST['texto']}' or concat(apellidos,' ', nombres) like '{$_POST['texto']}%' or concat( nombres,' ',apellidos) like '{$_POST['texto']}%') and activo = 1 order by apellidos;");
	if($sql->execute()){
		while($rows = $sql->fetch(PDO::FETCH_ASSOC))
			$filas[] = $rows;
		echo json_encode( array('alumnos' => $filas, 'mensaje' => 'ok'));
	}
}

function eliminarAlumno($db){
	$sql = $db->prepare("UPDATE `alumno` SET `activo` = '0' WHERE `id` = ?;");
	if($sql->execute([ $_POST['id'] ])){
		$sqlRelacion = $db->prepare("UPDATE `relacion` SET `activo` = '0' WHERE `idAlumno` = ?;");
		$sqlRelacion->execute([ $_POST['id'] ]);
		echo json_encode( array('mensaje' => 'eliminado ok'));
	}
}