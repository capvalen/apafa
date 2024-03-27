<?php
include('conexion.php');

switch ($_POST['pedir']) {
	case 'listar': listar($datab); break;
	case 'listarWeb': listarWeb($datab); break;
	default: break;
}

function listar($db){
	$filas = [];
	$sql = $db->prepare("SELECT *, g.id as idGrado FROM `grados` as g inner join niveles as n on g.idNivel = n.id;");
	if($sql->execute()){
		while($rows = $sql->fetch(PDO::FETCH_ASSOC))
			$filas[] = $rows;
		echo json_encode( array('grados' => $filas, 'mensaje' => 'ok'));
	}
}

function listarWeb($db){
	$filas = [];
	$sql = $db->prepare("SELECT *, g.id as idGrado FROM `grados` as g inner join niveles as n on g.idNivel = n.id where g.id <>18 order by nivel asc, numero asc;");
	if($sql->execute()){
		while($rows = $sql->fetch(PDO::FETCH_ASSOC))
			$filas[] = $rows;
		echo json_encode( array('grados' => $filas, 'mensaje' => 'ok'));
	}
}
