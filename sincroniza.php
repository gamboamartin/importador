<?php

use base\conexion;
use gamboamartin\errores\errores;
use gamboamartin\importador\models\imp_database;

require "init.php";
require 'vendor/autoload.php';

$con = new conexion();
$link = conexion::$link;
$imp_database_modelo = (new imp_database(link: $link));
$_SESSION['usuario_id'] = 2;
$databases = $imp_database_modelo->registros();
if(errores::$error){
    $error = (new errores())->error(mensaje: 'Error',data:  $databases);
    print_r($error);
    exit;
}

foreach ($databases as $database){

    $alta = $imp_database_modelo->alta_full(imp_database_id: $database['imp_database_id']);
    if(errores::$error){
        $error = (new errores())->error(mensaje: 'Error',data:  $alta);
        print_r($error);
        exit;
    }
    print_r($alta);
    echo "<br><br>";

}
