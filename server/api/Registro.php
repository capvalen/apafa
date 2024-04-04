<?php
include('conexion.php');
/* $_POST = json_decode(file_get_contents('php://input'),true);
( $_SERVER['REQUEST_METHOD'] === 'OPTIONS' )? die() : ''; */
//var_dump($_POST); die();

switch ($_POST['pedir']) {
	case 'registro': prematricula($datab); break;
	default: break;
}

function prematricula($db){
	$idPadre=-1;	$idMadre=-1;	$idApoderado=-1; $matriculados=0;
	$pasante = json_decode($_POST['alumno'], true);
	$pasante2 = json_decode($_POST['alumno2'], true);
	$pasante3 = json_decode($_POST['alumno3'], true);
	$pasante4 = json_decode($_POST['alumno4'], true);
	$pasante5 = json_decode($_POST['alumno5'], true);
	$pasante6 = json_decode($_POST['alumno6'], true);
	$pasante7 = json_decode($_POST['alumno7'], true);


	
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

	
	// ----- Registro del apoderado
	$apoderado = json_decode($_POST['apoderado'], true);
	$idRelacion = $apoderado['idRelacion'];
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

	if($pasante['dni']<>'' && $pasante['apellidos']<>'' ){ registro($db, $pasante, $idPadre, $idMadre, $idApoderado, $idRelacion ); $matriculados++;}
	if($pasante2['dni']<>'' && $pasante2['apellidos']<>'' ){ registro($db, $pasante2, $idPadre, $idMadre, $idApoderado, $idRelacion ); $matriculados++;}
	if($pasante3['dni']<>'' && $pasante3['apellidos']<>'' ){ registro($db, $pasante3, $idPadre, $idMadre, $idApoderado, $idRelacion ); $matriculados++;}
	if($pasante4['dni']<>'' && $pasante4['apellidos']<>'' ){ registro($db, $pasante4, $idPadre, $idMadre, $idApoderado, $idRelacion ); $matriculados++;}
	if($pasante5['dni']<>'' && $pasante5['apellidos']<>'' ){ registro($db, $pasante5, $idPadre, $idMadre, $idApoderado, $idRelacion ); $matriculados++;}
	if($pasante6['dni']<>'' && $pasante6['apellidos']<>'' ){ registro($db, $pasante6, $idPadre, $idMadre, $idApoderado, $idRelacion ); $matriculados++;}
	if($pasante7['dni']<>'' && $pasante7['apellidos']<>'' ){ registro($db, $pasante7, $idPadre, $idMadre, $idApoderado, $idRelacion ); $matriculados++;}

	echo json_encode( array(
		'idPadre' => $idPadre, 'idMadre' => $idMadre, 'idApoderado' => $idApoderado,
		'matriculados' => $matriculados
	));
}

function registro($db, $alumno, $idPadre, $idMadre, $idApoderado, $idRelacion){
	
	// ----- Registro del alumno
	$idAlumno=-1;
	
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


	if($idPadre<>-1){
		$sqlRelaciones = $db->prepare("SELECT * FROM `relacion` where idPadre = ? and idAlumno = ? and idRelacion = 1 and activo = 1");
		$sqlRelaciones -> execute([ $idPadre, $idAlumno ]);
		$cuentaRelacion = $sqlRelaciones->rowCount();
		if($cuentaRelacion == 0 && $idAlumno<>-1){
			$sqlParentezco = $db->prepare("INSERT INTO `relacion`(`idPadre`, `idAlumno`, `idRelacion`) VALUES (?, ?, 1);");
			$sqlParentezco->execute([ $idPadre, $idAlumno ]);
		}
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

	if($idApoderado<>-1){
		$sqlRelacionesApoderado = $db->prepare("SELECT * FROM `relacion` where idPadre = ? and idAlumno = ? and idRelacion = ? and activo = 1");
		$sqlRelacionesApoderado -> execute([ $idApoderado, $idAlumno, $idRelacion ]);
		$cuentaRelacion = $sqlRelacionesApoderado->rowCount();
		if($cuentaRelacion == 0 && $idAlumno<>-1){
			$sqlParentezco = $db->prepare("INSERT INTO `relacion`(`idPadre`, `idAlumno`, `idRelacion`) VALUES (?, ?, ?);");
			$sqlParentezco->execute([ $idApoderado, $idAlumno, $idRelacion ]);
		}
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