<?php
include('conexion.php');

switch ($_POST['queReporte']) {
	case '1': padresAptos($datab); break;
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