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
	
	// ----- Registro del alumno
	$idAlumno=-1;$idPadre=-1;	$idMadre=-1;	$idApoderado=-1;
	$alumno = json_decode($_POST['alumno'], true);
	if($alumno['id']<>-1){ //alumno ya registrado, actualizamos datos
		$sqlAlumno = $db->prepare("UPDATE `alumno` SET `apellidos` = ?, `nombres` = ? WHERE `alumno`.`id` = ?; ");
		$sqlAlumno -> execute([ $alumno['apellidos'], $alumno['nombres'], $alumno['id'] ]);
		$idAlumno = $alumno['id'];
	}
	else if($alumno['dni']<>'' && $alumno['apellidos']<>''){
		//Comenzamos a registrar un nuevo alumno
		$sqlAlumno = $db->prepare("INSERT INTO `alumno`(`nombres`, `apellidos`, `dni`) VALUES (?, ?, ?);");
		$sqlAlumno -> execute([ $alumno['nombres'], $alumno['apellidos'], $alumno['dni'] ]);
		$idAlumno = $db->lastInsertId();
	}
	$año = date('Y') ;
	$sqlMatricula = $db->prepare("SELECT * FROM `matricula` where año = ?  and idAlumno = ? and idGrado = ? and activo = 1;");
	$sqlMatricula->execute([ $año, $idAlumno, $alumno['idGrado'] ]);
	
	$matriculas =  $sqlMatricula->rowCount();//Cantidad de veces que se matriculó este año
	if($matriculas == 0){
		$sqlInscripcion = $db->prepare("INSERT INTO `matricula`(`idAlumno`, `idGrado`, `año`) VALUES (?,?,?)");
		$sqlInscripcion->execute([ $idAlumno, $alumno['idGrado'], $año ]);
	}

	// ----- Registro del padre
	$padre = json_decode($_POST['padre'], true);
	if($padre['id']<>-1){ //padre ya registrado, actualizamos datos
		$sqlPadre = $db->prepare("UPDATE `padre` SET `apellidos` = ?, `nombres` = ? WHERE `id` = ?; ");
		$sqlPadre -> execute([ $padre['apellidos'], $padre['nombres'], $padre['id'] ]);
		$idPadre = $padre['id'];
	}
	else if($padre['dni']<>'' && $padre['apellidos']<>''){
		//Comenzamos a registrar un nuevo padre
		$sqlPadre = $db->prepare("INSERT INTO `padre`(`nombres`, `apellidos`, `dni`) VALUES (?, ?, ?);");
		$sqlPadre -> execute([ $padre['nombres'], $padre['apellidos'], $padre['dni'] ]);
		$idPadre = $db->lastInsertId();
	}

	if($idPadre<>-1){
		$sqlRelaciones = $db->prepare("SELECT * FROM `relacion` where idPadre = ? and idAlumno = ? and idRelacion = 1 and activo = 1");
		$sqlRelaciones -> execute([ $idPadre, $idAlumno ]);
		$cuentaRelacion = $sqlRelaciones->rowCount();
		if($cuentaRelacion == 0 && $idAlumno<>-1){
			$sqlParentezco = $db->prepare("INSERT INTO `relacion`(`idPadre`, `idAlumno`, `idRelacion`) VALUES (?, ?, 1);");
			$sqlParentezco->execute([ $idPadre, $idAlumno ]);
		}
	}

	// ----- Registro del madre
	$madre = json_decode($_POST['madre'], true);
	if($madre['id']<>-1){ //madre ya registrado, actualizamos datos
		$sqlMadre = $db->prepare("UPDATE `padre` SET `apellidos` = ?, `nombres` = ? WHERE `id` = ?; ");
		$sqlMadre -> execute([ $madre['apellidos'], $madre['nombres'], $madre['id'] ]);
		$idMadre = $madre['id'];
	}
	else if($madre['dni']<>'' && $madre['apellidos']<>''){
		//Comenzamos a registrar un nuevo madre
		$sqlMadre = $db->prepare("INSERT INTO `padre`(`nombres`, `apellidos`, `dni`) VALUES (?, ?, ?);");
		$sqlMadre -> execute([ $madre['nombres'], $madre['apellidos'], $madre['dni'] ]);
		$idMadre = $db->lastInsertId();
	}

	if($idMadre<>-1){
		$sqlRelacionesMadre = $db->prepare("SELECT * FROM `relacion` where idPadre = ? and idAlumno = ? and idRelacion = 2 and activo = 1");
		$sqlRelacionesMadre -> execute([ $idMadre, $idAlumno ]);
		$cuentaRelacion = $sqlRelacionesMadre->rowCount();
		if($cuentaRelacion == 0 && $idAlumno<>-1){
			$sqlParentezco = $db->prepare("INSERT INTO `relacion`(`idPadre`, `idAlumno`, `idRelacion`) VALUES (?, ?, 2);");
			$sqlParentezco->execute([ $idMadre, $idAlumno ]);
		}
	}

	// ----- Registro del apoderado
	$apoderado = json_decode($_POST['apoderado'], true);
	if($apoderado['id']<>-1){ //apoderado ya registrado, actualizamos datos
		$sqlApoderado = $db->prepare("UPDATE `padre` SET `apellidos` = ?, `nombres` = ? WHERE `id` = ?; ");
		$sqlApoderado -> execute([ $apoderado['apellidos'], $apoderado['nombres'], $apoderado['id'] ]);
		$idApoderado = $apoderado['id'];
	}
	else if($apoderado['dni']<>'' && $apoderado['apellidos']<>''){
		//Comenzamos a registrar un nuevo apoderado
		$sqlApoderado = $db->prepare("INSERT INTO `padre`(`nombres`, `apellidos`, `dni`, `idRelacion`) VALUES (?, ?, ?, ?);");
		$sqlApoderado -> execute([ $apoderado['nombres'], $apoderado['apellidos'], $apoderado['dni'], $apoderado['idRelacion'] ]);
		$idApoderado = $db->lastInsertId();
	}

	if($idApoderado<>-1){
		$sqlRelacionesApoderado = $db->prepare("SELECT * FROM `relacion` where idPadre = ? and idAlumno = ? and idRelacion = ? and activo = 1");
		$sqlRelacionesApoderado -> execute([ $idApoderado, $idAlumno, $apoderado['idRelacion'] ]);
		$cuentaRelacion = $sqlRelacionesApoderado->rowCount();
		if($cuentaRelacion == 0 && $idAlumno<>-1){
			$sqlParentezco = $db->prepare("INSERT INTO `relacion`(`idPadre`, `idAlumno`, `idRelacion`) VALUES (?, ?, ?);");
			$sqlParentezco->execute([ $idApoderado, $idAlumno, $apoderado['idRelacion'] ]);
		}
	}

	
	echo json_encode( array(
		'idAlumno' => $idAlumno, 'idPadre' => $idPadre, 'idMadre' => $idMadre, 'idApoderado' => $idApoderado
	));
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