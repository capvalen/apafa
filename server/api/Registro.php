<?php
include('conexion.php');
/* $_POST = json_decode(file_get_contents('php://input'),true);
( $_SERVER['REQUEST_METHOD'] === 'OPTIONS' )? die() : ''; */
//var_dump($_POST); die();

switch ($_POST['pedir']) {
	case 'registro': registro($datab); break;
	default: break;
}

function registro($db){
	
	$alumno = json_decode($_POST['alumno'], true);
	if($alumno['id']<>-1){ //alumno ya registrado, actualizamos datos
		$sqlAlumno = $db->prepare("UPDATE `alumno` SET `apellidos` = ?, `nombres` = ? WHERE `alumno`.`id` = ?; ");
		$sqlAlumno -> execute([ $alumno['apellidos'], $alumno['nombres'], $alumno['id'] ]);
		$idAlumno = $alumno['id'];
	}
	else if($alumno['dni']<>'' && $alumno['apellidos']<>''){
		echo 'co';
		//Comenzamos a registrar un nuevo alumno
		$sqlAlumno = $db->prepare("INSERT INTO `alumno`(`nombres`, `apellidos`, `dni`) VALUES (?, ?, ?);");
		$sqlAlumno -> execute([ $alumno['nombres'], $alumno['apellidos'], $alumno['dni'] ]);
		$idAlumno = $db->lastInsertId();
	}
	echo 'idA '.$idAlumno; die();
		
	
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



?>