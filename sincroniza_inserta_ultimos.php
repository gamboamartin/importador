<?php

$file_lock = 'sincroniza_inserta_ultimos.lock';

if(file_exists($file_lock)){
    echo 'Se esta corriendo servicio';
    exit;
}
else{
    file_put_contents($file_lock, '');
}

use base\conexion;
use gamboamartin\errores\errores;
use gamboamartin\importador\models\imp_database;

require "init.php";
require 'vendor/autoload.php';



$con = new conexion();
$link = conexion::$link;
$imp_database_modelo = (new imp_database(link: $link));
$_SESSION['usuario_id'] = 2;
$_SESSION['session_id'] = mt_rand(10000000,99999999);
$_GET['session_id'] = $_SESSION['session_id'];


$databases = $imp_database_modelo->registros();
if(errores::$error){
    $error = (new errores())->error(mensaje: 'Error',data:  $databases);
    print_r($error);
    unlink($file_lock);
    exit;
}

foreach ($databases as $database){

    $alta = $imp_database_modelo->inserta_ultimos(imp_database_id: $database['imp_database_id']);
    if(errores::$error){
        $error = (new errores())->error(mensaje: 'Error',data:  $alta);
        unlink($file_lock);
        print_r($error);
        exit;
    }
    print_r($alta);
    echo "<br><br>";

}

unlink($file_lock);
exit;
