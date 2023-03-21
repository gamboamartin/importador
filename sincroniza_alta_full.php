<?php

$file_lock = 'sincroniza_alta_full.lock';

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
    $out = $imp_database_modelo->alta_full(imp_database_id: $database['imp_database_id']);
    if(errores::$error){
        $error = (new errores())->error(mensaje: 'Error',data:  $out);
        unlink($file_lock);
        print_r($error);
        exit;
    }
    echo '<br>----------DATABASE-------------------<br>';
    echo "database origen: <b>$database[imp_database_descripcion]</b> ";
    echo "server origen: <b>$database[imp_server_descripcion]</b>";
    foreach ($out as $data){

        echo "imp_destino_id: <b>$data->imp_destino_id</b>";
        echo "imp_destino_descripcion: <b>$data->imp_destino_descripcion </b> ";
        echo '<br>----------EJECUCIONES-------------------<br>';
        foreach ($data->r_alta_full->ejecuciones as $ejecucion){

            $row_id = $ejecucion->data->row['id'];
            $aplica_alta = $ejecucion->alta->aplica_alta;
            echo "Id Origen: <b>$row_id </b> ";
            echo "Aplica Alta: <b>$aplica_alta</b> ";
            echo "<br><br>";
        }
    }
    echo "<br>-------------------------------------------<br>";
}

unlink($file_lock);
exit;
