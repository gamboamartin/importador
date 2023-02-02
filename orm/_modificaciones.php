<?php
namespace gamboamartin\importador\models;
use base\orm\modelo;
use gamboamartin\errores\errores;
use PDO;
use stdClass;

class _modificaciones{

    private errores $error;
    public function __construct(){
        $this->error = new errores();
    }

    final public function aplica_modificaciones(string $adm_accion_descripcion, array $imp_destino, PDO $link, int $usuario_id){
        $name_model = (new _namespace())->name_model(imp_destino: $imp_destino);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener name_modelo',data:  $name_model);
        }

        $rows = (new _modelado())->rows_origen(adm_accion_descripcion: $adm_accion_descripcion,
            imp_origen_id: $imp_destino['imp_origen_id'], limit: 1, link: $link, name_model: $name_model);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener rows',data:  $rows);
        }

        $ejecuciones = $this->ejecuta_modificaciones(imp_database_id: $imp_destino['imp_database_id'], link: $link,
            name_model: $name_model, rows: $rows, usuario_id: $usuario_id);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al modificar registros',data:  $ejecuciones);
        }
        return $ejecuciones;
    }

    final public function aplica_modificaciones_ultimas_id(array $imp_destino, PDO $link, int $usuario_id){
        $name_model = (new _namespace())->name_model(imp_destino: $imp_destino);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener name_modelo',data:  $name_model);
        }

        $rows = (new _modelado())->rows_origen_ultimos(campo: 'id',
            imp_origen_id: $imp_destino['imp_origen_id'], limit: 1, link: $link, name_model: $name_model);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener rows',data:  $rows);
        }

        $ejecuciones = $this->ejecuta_modificaciones(imp_database_id: $imp_destino['imp_database_id'], link: $link,
            name_model: $name_model, rows: $rows, usuario_id: $usuario_id);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al modificar registros',data:  $ejecuciones);
        }
        return $ejecuciones;
    }

    private function ejecuta_modificacion_destino(modelo $destino, array $row): array|stdClass
    {
        $data = (new _data())->data_modifica_destino(destino: $destino,row:  $row);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener datos',data:  $data);
        }

        $alta = new stdClass();
        $alta->data_parents = new stdClass();
        $alta->data_parents->existe_parent =false;
        $alta->data_parents->aplica_parents =true;
        if($data->existe) {

            $alta = $this->modifica_destino(destino: $destino,row:  $data->row);
            if(errores::$error){
                return $this->error->error(mensaje: 'Error al modificar registro',data:  $alta);
            }
        }
        else{
            $alta = (new _inserciones())->inserta_destino(destino: $destino,row:  $data->row);
            if(errores::$error){
                return $this->error->error(mensaje: 'Error al insertar registro',data:  $alta);
            }
        }

        $retorno = new stdClass();
        $retorno->data = $data;
        $retorno->alta = $alta;



        return $retorno;
    }

    private function ejecuta_modificaciones(int $imp_database_id,PDO $link, string $name_model, array $rows, int $usuario_id){
        $destino = (new _modelado())->destino(imp_database_id: $imp_database_id, link: $link, name_model: $name_model);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al conectar con destino',data:  $destino);
        }
        $destino->usuario_id = $usuario_id;
        $destino->valida_user = false;

        $ejecuciones = $this->modifica_destinos(destino: $destino, rows: $rows);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al modificar registros',data:  $ejecuciones);
        }
        return $ejecuciones;
    }

    private function modifica_destino(modelo $destino, array $row): array|stdClass
    {
        $retorno = new stdClass();

        $data_parents = (new _parents())->data_parents(destino: $destino, row: $row);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener parents registro',data:  $data_parents);
        }

        $retorno->data_parents = $data_parents;
        if(!$data_parents->existe_parent && $data_parents->aplica_parents){
            return $retorno;
        }

        $alta = $destino->modifica_bd(registro: $row, id: $row['id']);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al modificar registro',data:  $alta);
        }

        $retorno->alta = $alta;
        return $retorno;

    }

    private function modifica_destinos(modelo $destino, array $rows){
        $ultimo_id_origen = 0;
        $ejecuciones = array();
        foreach ($rows as $row){
            $ultimo_id_origen = $row['id'];
            $exe = $this->ejecuta_modificacion_destino(destino: $destino,row:  $row);
            if(errores::$error){
                return $this->error->error(mensaje: 'Error al modificar registro',data:  $exe);
            }
            $ejecuciones[] = $exe;
            if(!$exe->alta->data_parents->existe_parent && $exe->alta->data_parents->aplica_parents){
                break;
            }

        }
        $data = new stdClass();
        $data->ultimo_id_origen = $ultimo_id_origen;
        $data->ejecuciones = $ejecuciones;

        return $data;
    }


}
