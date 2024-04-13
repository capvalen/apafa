<?php
include('conexion.php');
/* $_POST = json_decode(file_get_contents('php://input'),true);
( $_SERVER['REQUEST_METHOD'] === 'OPTIONS' )? die() : ''; */
//var_dump($_POST); die();

switch ($_POST['pedir']) {
	case 'buscarDNI': buscarDNI($datab); break;
	case 'crearApoderado': crearApoderado($datab); break;
	case 'listar30Apoderados': listar30Apoderados($datab); break;
	case 'buscarApoderados': buscarApoderados($datab); break;
	case 'eliminarApoderado': eliminarApoderado($datab); break;
	case 'registrarAsistencia': registrarAsistencia($datab); break;
	default: break;
}

function buscarDNI($db){
	$sql = $db->prepare("SELECT * FROM `padre` WHERE dni = ? and activo = 1 limit 1;");
	if($sql->execute([ $_POST['dni'] ])){
		$conteo =  $sql->rowCount();
		$apoderado = [];
		$hijos = [];
		if($conteo>0){
			$rows = $sql->fetch(PDO::FETCH_ASSOC);
			$apoderado = $rows;
			
			$sqlHijos = $db->prepare("SELECT * FROM `relacion` as r
			inner join alumno a on a.id = r.idAlumno
			where idPadre = ? and r.activo = 1;");
			$sqlHijos -> execute([ $apoderado['id'] ]);
			while($rowHijos = $sqlHijos->fetch(PDO::FETCH_ASSOC))
				$hijos[] = $rowHijos;
		}
		
		echo json_encode( array('conteo' => $conteo, 'apoderado' => $apoderado, 'mensaje' => 'ok', 'hijos' => $hijos));
	}
}

function crearApoderado($db){
	$apoderado = json_decode($_POST['apoderado'], true);
	$hijos = json_decode($_POST['hijos'], true);
	if( $apoderado['id'] == -1 ){
		$sql = $db->prepare("INSERT INTO `padre`(`nombres`, `apellidos`, `dni`, `celular`, `idRelacion`) VALUES (?, ?, ?, ?, ?)");
		if($sql->execute([ $apoderado['nombres'], $apoderado['apellidos'], $apoderado['dni'], $apoderado['celular'], $apoderado['idRelacion'], ])){
			$idPadre = $db->lastInsertId();
			
			foreach ($hijos as $hijo) {
				if($hijo['id']==-1){
					$sqlHijo = $db->prepare("INSERT INTO `alumno`( `nombres`, `apellidos`, `dni`) VALUES ( ?, ?, ? )");
					$sqlHijo->execute([ $hijo['nombres'], $hijo['apellidos'], $hijo['dni'] ]);
					$idHijo = $db->lastInsertId();
				}else
					$idHijo = $hijo['id'];

				//Comprobar si no esta registrado
				$sqlExiste = $db->prepare("SELECT * FROM `relacion` where idPadre =? and idAlumno = ? and activo = 1;");
				$sqlExiste->execute([ $idPadre, $idHijo]);
				$conteo = $sql->fetch(PDO::FETCH_ASSOC);
				if($conteo == 0){
					$sqlRelacion = $db->prepare("INSERT INTO `relacion`(`idPadre`, `idAlumno`, `idRelacion`) VALUES (?, ?, ?)");
					$sqlRelacion -> execute([ $idPadre, $idHijo, $apoderado['idRelacion'] ]);
				}
			}

			echo json_encode( array('idPadre' => $idPadre, 'mensaje' => 'ok'));
		}else{
			echo 'error';
		}
	}else{
		echo json_encode( array('idPadre' => $apoderado['id'], 'mensaje' => 'ok'));
	}
}

function listar30Apoderados($db){
	$filas = [];
	$sql = $db->prepare("SELECT * FROM `padre` WHERE activo = 1 order by id desc limit 30;");
	if($sql->execute()){
		while($rows = $sql->fetch(PDO::FETCH_ASSOC))
			$filas[] = $rows;
		echo json_encode( array('apoderados' => $filas, 'mensaje' => 'ok'));
	}
}

function buscarApoderados($db){
	$filas = [];
	$sql = $db->prepare("SELECT * FROM `padre` WHERE (dni = '{$_POST['texto']}' or concat(apellidos,' ', nombres) like '{$_POST['texto']}%' or concat( nombres,' ',apellidos) like '{$_POST['texto']}%' or celular ='{$_POST['texto']}')  and activo = 1 order by apellidos asc;");
	if($sql->execute()){
		while($rows = $sql->fetch(PDO::FETCH_ASSOC))
			$filas[] = $rows;
		echo json_encode( array('apoderados' => $filas, 'mensaje' => 'ok'));
	}
}

function eliminarApoderado($db){
	$sql = $db->prepare("UPDATE `padre` SET `activo` = '0' WHERE `id` = ?;");
	if($sql->execute([ $_POST['id'] ])){
		$sqlRelacion = $db->prepare("UPDATE `relacion` SET `activo` = '0' WHERE `idPadre` = ?;");
		$sqlRelacion->execute([ $_POST['id'] ]);
		echo json_encode( array('mensaje' => 'eliminado ok'));
	}
}

function registrarAsistencia($db){
	$sql = $db->prepare("INSERT INTO `asistencia`(`idPadre`, `idReunion`, `observaciones`) VALUES (?, ?, '');");
	if($sql->execute([ $_POST['idPadre'], $_POST['idReunion'] ]))
		echo json_encode( array('mensaje' => 'ok'));
}

?>