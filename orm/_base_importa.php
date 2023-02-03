<?php
namespace gamboamartin\importador\models;

use gamboamartin\errores\errores;
use PDO;
use stdClass;

class _base_importa{
    private errores $error;
    public function __construct(){
        $this->error = new errores();
    }

    public function aplica_inserciones(string $campo, int $imp_destino_id, PDO $link, int $usuario_id){


        $data = $this->data_por_campo(campo: $campo,imp_destino_id:  $imp_destino_id, link: $link);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener data',data:  $data);
        }

        $ejecuciones = (new _inserciones())->ejecuta_inserciones(imp_database_id: $data->imp_destino['imp_database_id'], link: $link,
            name_model: $data->name_model, rows: $data->rows, usuario_id: $usuario_id);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al insertar registros',data:  $ejecuciones);
        }
        return $ejecuciones;
    }

    public function aplica_modificaciones(string $campo, int $imp_destino_id, PDO $link, int $usuario_id){

        $data = $this->data_por_campo(campo: $campo,imp_destino_id:  $imp_destino_id, link: $link);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener data',data:  $data);
        }

        $ejecuciones = (new _modificaciones())->ejecuta_modificaciones(imp_database_id: $data->imp_destino['imp_database_id'], link: $link,
            name_model: $data->name_model, rows: $data->rows, usuario_id: $usuario_id);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al insertar registros',data:  $ejecuciones);
        }
        return $ejecuciones;
    }
    public function data_origen(string $adm_accion_descripcion, array $imp_destino, PDO $link){
        $name_model = (new _namespace())->name_model(imp_destino: $imp_destino);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener name_modelo',data:  $name_model);
        }

        $rows = (new _modelado())->rows_origen(adm_accion_descripcion: $adm_accion_descripcion,
            imp_destino_id: $imp_destino['imp_destino_id'], imp_origen_id: $imp_destino['imp_origen_id'],
            limit: 1, link: $link, name_model: $name_model);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener rows',data:  $rows);
        }

        $data = new stdClass();
        $data->name_model = $name_model;
        $data->rows = $rows;

        return $data;
    }

    public function data_origen_campo(string $campo, array $imp_destino, PDO $link){
        $name_model = (new _namespace())->name_model(imp_destino: $imp_destino);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener name_modelo',data:  $name_model);
        }

        $rows = (new _modelado())->rows_origen_ultimos(campo: $campo,
            imp_origen_id: $imp_destino['imp_origen_id'], limit: 1, link: $link, name_model: $name_model);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener rows',data:  $rows);
        }

        $data = new stdClass();
        $data->name_model = $name_model;
        $data->rows = $rows;
        return $data;
    }

    private function data_por_campo(string $campo, int $imp_destino_id, PDO $link){
        $imp_destino = (new imp_destino(link: $link))->registro(registro_id: $imp_destino_id);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener destino',data:  $imp_destino);
        }
        $data = $this->data_origen_campo(campo: $campo,imp_destino:  $imp_destino,link:  $link);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener data',data:  $data);
        }
        $data->imp_destino = $imp_destino;
        return $data;
    }
}
