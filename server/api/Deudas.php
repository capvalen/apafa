<?php
include('conexion.php');

switch ($_POST['pedir']) {
	case 'listar': listar($datab); break;
	case 'detalles': detalles($datab); break;
	case 'guardarPago': guardarPago($datab); break;
	case 'borrar': borrar($datab); break;
	default: break;
}

function listar($db){
	$filas = [];
	$sql = $db->prepare("SELECT d.*, g.numero, g.idNivel, g.grado FROM `deudas` as d inner join grados as g on g.id = d.idGrado  where d.activo = 1 order by d.fecha desc;");
	if($sql->execute()){
		while($rows = $sql->fetch(PDO::FETCH_ASSOC))
			$filas[] = $rows;
		echo json_encode( array('deudas' => $filas, 'mensaje' => 'ok'));
	}
}

function detalles($db){
	$filas = [];
	$sql = $db->prepare("SELECT d.*, g.numero, g.idNivel, g.grado FROM `deudas` as d inner join grados g on g.id = d.idGrado where d.id = ?;");
	if($sql->execute([ $_POST['idDeuda'] ])){
		while($rows = $sql->fetch(PDO::FETCH_ASSOC))
			$filas[] = $rows;

		$asistentes=[];
		$sqlAsistentes = $db->prepare("SELECT *, pa.id as idPago FROM `pagos` as pa inner join padre as p on pa.idPadre = p.id where idDeuda = ? and pa.activo=1;");
		$sqlAsistentes-> execute([ $_POST['idDeuda']]);
		while($rowsAsistentes = $sqlAsistentes->fetch(PDO::FETCH_ASSOC))
			$asistentes[] = $rowsAsistentes;
		echo json_encode( array('deuda' => $filas[0], 'asistentes' => $asistentes, 'mensaje' => 'ok'));
	}
}

function guardarPago($db){
	$sql = $db->prepare("INSERT INTO `pagos`(`idDeuda`, `idPadre`, `monto`, `observacion`) VALUES (?, ?, ?, ?)");
	if($sql->execute([ $_POST['idDeuda'], $_POST['idPadre'], $_POST['monto'], $_POST['observacion'] ])){
		$idPago = $db->lastInsertId();
		echo json_encode( array('idPago' => $idPago, 'mensaje' => 'ok'));
	}
}

function borrar($db){
	$sql = $db->prepare("UPDATE `pagos` SET `activo` = '0' WHERE `id` = ?;");
	if($sql->execute([ $_POST['idPago'] ])){
		echo json_encode( array('mensaje' => 'eliminado ok'));
	}
}