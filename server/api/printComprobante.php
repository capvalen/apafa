<?php

require __DIR__ . '/vendor/autoload.php';
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;

use Mike42\Escpos\EscposImage; //librería de imagen


include "vendor/autoload.php";

$connectorV31 = new WindowsPrintConnector("smb://127.0.0.1/POS");
try {
    $printer = new Printer($connectorV31);


    $tux = EscposImage::load("../images/logo.jpg", false);
    
    $printer = new Printer($connectorV31);
    $printer->setJustification(Printer::JUSTIFY_CENTER);
    $printer -> bitImage($tux);
    $printer -> setEmphasis(true);
    
    $printer -> text("------ APORTE DE PAGO -------\n");
    $printer -> setEmphasis(false);
    $printer->setJustification(Printer::JUSTIFY_LEFT);
    $printer -> text("Fecha: ". $_POST['fecha'] ."\n");
    $printer -> text("Doc. Identidad: {$_POST['dni']}\n");
    $printer -> text("Señor(a): ".strtoupper($_POST['cliente'])."\n");
		
    $printer->setJustification(Printer::JUSTIFY_CENTER);
    $printer -> text("------------------------------\n");
    $printer->setJustification(Printer::JUSTIFY_LEFT);
		$printer -> text("Concepto: {$_POST['motivo']}\n");
		$printer -> text("Total a pagar: S/ {$_POST['total']}\n");
		$printer -> text("Pagado: S/ {$_POST['monto']}\n");
		$printer -> text("Obs.: S/ {$_POST['observacion']}\n");
    $printer->setJustification(Printer::JUSTIFY_CENTER);
    $printer -> text("------------------------------\n");
    $printer -> cut();
    // Close printer 
    $printer -> close();
} catch (Exception $e) {
    echo "No se pudo imprimir en la impresora: " . $e -> getMessage() . "\n";
}