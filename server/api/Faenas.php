<?php
include('conexion.php');

switch ($_POST['pedir']) {
	case 'crear': crear($datab); break;
   	case 'listar': listar($datab); break;
    case 'detalles': detalles($datab); break;
    case 'agregarTrabajador': agregarTrabajador($datab); break;
	default: break;
}

function crear($db){
	$faena = json_decode($_POST['faena'], true);
	$sql = $db->prepare("INSERT INTO `faenas`(`asunto`, `detalles`, `inicio`, `salida`) VALUES (?, ?, ?, ?)");
	if($sql->execute([ $faena['asunto'], $faena['detalles'], $faena['fecha'].' '. $faena['inicio'], $faena['fecha'].' '. $faena['salida'] ])){
		$idFaena= $db->lastInsertId();
		echo json_encode( array('id' => $idFaena, 'mensaje' => 'ok'));
	}else{
        echo 'error';
        $sql->debugDumpParams();
    }
}

function listar($db){
	$filas = [];
	$sql = $db->prepare("SELECT `id`, `asunto`, `detalles`, DATE(inicio) as fecha, TIME(inicio) as inicio, time( `salida` ) as salida, `activo` FROM `faenas` WHERE activo = 1 limit 50;");
	if($sql->execute()){
		while($rows = $sql->fetch(PDO::FETCH_ASSOC))
			$filas[] = $rows;
		echo json_encode( array('faenas' => $filas, 'mensaje' => 'ok'));
	}
}

function detalles($db){
	$filas = []; $trabajadores = [];
	$sql = $db->prepare("SELECT `id`, `asunto`, `detalles`, DATE(inicio) as fecha, TIME(inicio) as inicio, time( `salida` ) as salida, `activo` FROM `faenas` where id= ?;");
	if($sql->execute([ $_POST['idFaena'] ])){
		while($rows = $sql->fetch(PDO::FETCH_ASSOC))
			$filas[] = $rows;
        $sqlTrabajador = $db->prepare("SELECT t.*, TIME(entrada) as ingreso, p.dni, p.apellidos, p.nombres FROM `trabajadores` t
            inner join padre p on p.id = t.idPadre
            where idFaena = ?;");
        $sqlTrabajador->execute([ $_POST['idFaena'] ]);
        while($rowsTrabajador = $sqlTrabajador->fetch(PDO::FETCH_ASSOC))
            $trabajadores[] = $rowsTrabajador;
		echo json_encode( array('faena' => $filas[0], 'trabajadores' => $trabajadores, 'mensaje' => 'ok'));
	}
}

function agregarTrabajador($db){
    $trabajador = [];

    $sql = $db->prepare("SELECT * from padre where dni = ? limit 1;");
    $sql->execute([$_POST['dni']]);

    $cantidad = $sql->rowCount();
    if($cantidad == 0 ){
        $sqlPadre = $db->prepare("INSERT INTO `padre`(`dni`,`idRelacion`) VALUES (?, 8)");
        $sqlPadre -> execute([ $_POST['dni'] ]);
        $idPadre = $db->lastInsertId();
        $padre = [];
    }else{
        $padre = $sql->fetch(PDO::FETCH_ASSOC);
        $idPadre = $padre['id'];
        $trabajador = $padre;
    }

    //echo 'idpa '. $idPadre;die();
    $sqlTrabajador = $db->prepare("INSERT INTO `trabajadores`(`idFaena`, `idPadre`, `entrada`, `minutos`) VALUES (?, ?, ?, ?);");
    $resp = $sqlTrabajador->execute([ $_POST['idFaena'], $idPadre, $_POST['entrada'], $_POST['minutos'] ]);


    if($resp){
        echo json_encode( array('trabajador' => $padre, 'mensaje' => 'ok'));
    }
    else{ echo 'error'; }


}
