<?php
namespace gamboamartin\importador\models;
use base\conexion;
use base\orm\modelo;
use gamboamartin\errores\errores;
use PDO;
use stdClass;

class _data{

    private errores $error;
    public function __construct(){
        $this->error = new errores();
    }

    /**
     * rev
     * @param modelo $destino
     * @param array $row
     * @return array|stdClass
     */
    final public function data_inserta_destino(modelo $destino, array $row): array|stdClass
    {
        $row = (new _modelado())->limpia_data_row(row: $row);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al limpiar row',data:  $row);
        }

        $existe = $destino->existe_by_id(registro_id: $row['id']);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al verificar si existe',data:  $existe);
        }
        $data = new stdClass();
        $data->row = $row;
        $data->existe = $existe;
        return $data;
    }

    final public function data_modifica_destino(modelo $destino, array $row): array|stdClass
    {
        $row = (new _modelado())->limpia_data_row(row: $row);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al limpiar row',data:  $row);
        }

        $existe = $destino->existe_by_id(registro_id: $row['id']);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al verificar si existe',data:  $existe);
        }
        $data = new stdClass();
        $data->row = $row;
        $data->existe = $existe;
        return $data;
    }





}
