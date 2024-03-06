<?php
include('conexion.php');

switch ($_POST['pedir']) {
	case 'listar': listar($datab); break;
	case 'buscar': buscar($datab); break;
	case 'crear': crear($datab); break;
	case 'detalles': detalles($datab); break;
	case 'guardarPago': guardarPago($datab); break;
	case 'borrar': borrar($datab); break;
	default: break;
}

function crear($db){
	$deuda = json_decode($_POST['deuda'], true);
	$sql = $db->prepare("INSERT INTO `deudas`(`motivo`, `detalle`, `monto`, `fecha`, `idGrado`) VALUES (?, ?, ?, ?, ?)");
	if($sql->execute([ $deuda['motivo'], $deuda['detalle'], $deuda['monto'], $deuda['fecha'], $deuda['idGrado'] ])){
		$iDeuda = $db->lastInsertId();
		echo json_encode( array('idDeuda' => $iDeuda, 'mensaje' => 'ok'));
	}
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

function buscar($db){
	$filas = [];
	if($_POST['texto']!=''){
		$sql = $db->prepare("SELECT d.*, g.numero, g.idNivel, g.grado, year(fecha) as anio, month(fecha) as mes FROM `deudas` as d inner join grados as g on g.id = d.idGrado  where d.activo = 1 and d.motivo like ? order by d.fecha desc;");
		$respuesta = $sql->execute([ "%".$_POST['texto']."%" ]);
	}
	else{
		$sql = $db->prepare("SELECT d.*, g.numero, g.idNivel, g.grado, year(fecha) as anio, month(fecha) as mes FROM `deudas` as d inner join grados as g on g.id = d.idGrado  where d.activo = 1 order by d.fecha desc;");
		$respuesta = $sql->execute();
	}
	if($respuesta){//echo $sql->debugDumpParams(); die();

		while($rows = $sql->fetch(PDO::FETCH_ASSOC)):
			//Evaluación de fechas:
			
			if($_POST['año']<>-1 && $_POST['mes']==-1){
				if($rows['anio'] == $_POST['año']) $filas[] = $rows;
			}else if( $_POST['año']<>-1 && $_POST['mes']<>-1 ){ echo 'aca';
				//var_dump($rows['anio'] == $_POST['año'] , $rows['mes'] == $_POST['mes'] ) ."\n";
				if($rows['anio'] == $_POST['año'] && $rows['mes'] == $_POST['mes'] ) $filas[] = $rows;
			}
			else $filas[] = $rows;	

			//Evaluación de grados:
			if($_POST['idGrado'] <> -1 && $rows['idGrado']<>$_POST['idGrado'] )
				 array_pop($filas);

		endwhile;

				//$filas[] = $rows;
		
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