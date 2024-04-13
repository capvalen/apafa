<?php
include('conexion.php');

switch ($_POST['queReporte']) {
	case '1': padresAptos($datab); break;
	case '2': padreVsReuniones($datab); break;
	case '3': padreVsFaenas($datab); break;
	case '4': conteos($datab); break;
	default: break;
}

function padresAptos($db){
	$filas = [];
	if($_POST['idGrado']<>-1){
		$sql = $db->prepare("SELECT m.*, p.*, tr.relacion, concat(a.apellidos,' ',a.nombres) as 'nombreAlumno'  FROM `matricula` m
		inner join relacion r on r.idAlumno = m.idAlumno
		inner join padre p on p.id = r.idPadre
		inner join alumno a on a.id = r.idAlumno
		inner join tiporelacion tr on tr.id = r.idRelacion
		where idGrado = ? and año = ? and r.activo = 1 and p.activo = 1;");
		$respuesta = $sql->execute([ $_POST['idGrado'], $_POST['año'] ]);
	}else{
		$sql = $db->prepare("SELECT m.*, p.*, tr.relacion, concat(a.apellidos,' ',a.nombres) as 'nombreAlumno'  FROM `matricula` m
		inner join relacion r on r.idAlumno = m.idAlumno
		inner join padre p on p.id = r.idPadre
		inner join alumno a on a.id = r.idAlumno
		inner join tiporelacion tr on tr.id = r.idRelacion
		where año = ? and r.activo = 1 and p.activo = 1;");
		$respuesta = $sql->execute([ $_POST['año'] ]);
	}
	if($respuesta){
		while($rows = $sql->fetch(PDO::FETCH_ASSOC))
			$filas[] = $rows;
		echo json_encode( $filas );
	}
}

function padreVsReuniones($db){ 
	$filas = [];

	$sqlPadre = $db->prepare("SELECT * from padre where dni = ? and activo = 1 limit 1;");
	$sqlPadre->execute([$_POST['dni']]);
	$padre = $sqlPadre->fetch(PDO::FETCH_ASSOC);
	$idPadre = $padre['id'];
	$existe = $sqlPadre->rowCount();

	$sqlReuniones = $db->prepare("SELECT * FROM `reunion`
	where year(fecha) = ? and activo = 1 order by id desc;");
	$sqlReuniones->execute([ $_POST['año'] ]);
	while($rowReuniones = $sqlReuniones->fetch(PDO::FETCH_ASSOC)){
		$sqlAsistencia =  $db->prepare("SELECT * FROM `asistencia`
		where idPadre = ? and idReunion = ? and activo = 1 limit 1;");
		//echo 'id '. $rowReuniones['id'];
		$sqlAsistencia->execute([$idPadre, $rowReuniones['id']]);
		$asistio = $sqlAsistencia->rowCount();
		if($asistio == 0){ //Falta
			$filas[] = array(
				'idReunion'=> $rowReuniones['id'],
				'asunto' => $rowReuniones['asunto'],
				'presente' => 0
			);
		}else{
			$rowAsistencia = $sqlAsistencia->fetch(PDO::FETCH_ASSOC);
			$filas[] = array(
				'idReunion'=> $rowReuniones['id'],
				'asunto' => $rowReuniones['asunto'],
				'presente' => 1,
				'fecha' => $rowAsistencia['registro']
			);
		}
	}
	echo json_encode( array('reuniones' => $filas, 'dni'=>$_POST['dni'], 'existe'=>$existe, 'apoderado' => $padre) ); 
}

function padreVsFaenas($db){ 
	$filas = [];

	$sqlPadre = $db->prepare("SELECT * from padre where dni = ? and activo = 1 limit 1;");
	$sqlPadre->execute([$_POST['dni']]);
	$padre = $sqlPadre->fetch(PDO::FETCH_ASSOC);
	$idPadre = $padre['id'];
	$existe = $sqlPadre->rowCount();

	$sqlFaenas = $db->prepare("SELECT `id`, `asunto`, `detalles`, DATE(inicio) as fecha, TIME(inicio) as inicio, time( `salida` ) as salida, `activo` FROM `faenas`
	where year(inicio) = ? and activo = 1 order by id desc;");
	$sqlFaenas->execute([ $_POST['año'] ]);
	while($rowFaenas = $sqlFaenas->fetch(PDO::FETCH_ASSOC)){
		$sqlTrabajador =  $db->prepare("SELECT *,  TIME(entrada) as ingreso FROM `trabajadores`
		where idPadre = ? and idFaena = ? and activo = 1 limit 1;");
		//echo 'id '. $rowFaenas['id'];
		$sqlTrabajador->execute([$idPadre, $rowFaenas['id']]);
		$asistio = $sqlTrabajador->rowCount();
		if($asistio == 0){ //Falta
			$filas[] = array(
				'idFaena'=> $rowFaenas['id'],
				'asunto' => $rowFaenas['asunto'],
				'presente' => 0
			);
		}else{
			$rowAsistencia = $sqlTrabajador->fetch(PDO::FETCH_ASSOC);
			$filas[] = array(
				'idFaena'=> $rowFaenas['id'],
				'asunto' => $rowFaenas['asunto'],
				'presente' => 1,
				'inicio'=> $rowFaenas['inicio'],
				'salida'=> $rowFaenas['salida'],
				'ingreso'=> $rowAsistencia['ingreso'],
				'minutos'=> $rowAsistencia['minutos'],
				'fecha' => $rowAsistencia['entrada']
			);
		}
	}
	echo json_encode( array('faenas' => $filas, 'dni'=>$_POST['dni'], 'existe'=>$existe, 'apoderado' => $padre) ); 
}

function conteos($db){
	$sqlAlumnado= $db->prepare("SELECT count(*) as conteo FROM `matricula`
	where año = year(now());");
	$sqlAlumnado->execute();
	$rowAlumnado = $sqlAlumnado->fetch(PDO::FETCH_ASSOC);
	$alumnosConPadres = $rowAlumnado['conteo'];

	$sqlPadres= $db->prepare("SELECT count(*) as conteo FROM `padre`
	where activo = 1");
	$sqlPadres->execute();
	$rowPadres = $sqlPadres->fetch(PDO::FETCH_ASSOC);
	$padresTotal = $rowPadres['conteo'];

	$sqlReuniones= $db->prepare("SELECT count(*) as conteo FROM `reunion`
	where activo = 1");
	$sqlReuniones->execute();
	$rowReuniones = $sqlReuniones->fetch(PDO::FETCH_ASSOC);
	$reunionesTotal = $rowReuniones['conteo'];

	$sqlFaenas= $db->prepare("SELECT count(*) as conteo FROM `faenas`
	where activo = 1");
	$sqlFaenas->execute();
	$rowFaenas = $sqlFaenas->fetch(PDO::FETCH_ASSOC);
	$faenasTotal = $rowFaenas['conteo'];

	$sqlDeudas= $db->prepare("SELECT count(*) as conteo FROM `deudas`
	where activo = 1");
	$sqlDeudas->execute();
	$rowDeudas = $sqlDeudas->fetch(PDO::FETCH_ASSOC);
	$deudasTotal = $rowDeudas['conteo'];

	echo json_encode( array('alumnosConPadres' => $alumnosConPadres, 'padresTotal'=> $padresTotal, 'reunionesTotal'=> $reunionesTotal, 'faenasTotal'=> $faenasTotal, 'deudasTotal'=> $deudasTotal ) ); 
}