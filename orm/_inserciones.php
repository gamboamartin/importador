<?php
namespace gamboamartin\importador\models;
use base\conexion;
use base\orm\modelo;
use gamboamartin\errores\errores;
use PDO;
use stdClass;

class _inserciones{

    private errores $error;
    public function __construct(){
        $this->error = new errores();
    }

    final public function aplica_inserciones(string $adm_accion_descripcion, array $imp_destino, PDO $link, int $usuario_id){
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

        $ejecuciones = $this->ejecuta_inserciones(imp_database_id: $imp_destino['imp_database_id'], link: $link,
            name_model: $name_model, rows: $rows, usuario_id: $usuario_id);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al insertar registros',data:  $ejecuciones);
        }
        return $ejecuciones;
    }

    final public function aplica_inserciones_ultimas(array $imp_destino, PDO $link, int $usuario_id){
        $name_model = (new _namespace())->name_model(imp_destino: $imp_destino);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener name_modelo',data:  $name_model);
        }

        $rows = (new _modelado())->rows_origen_ultimos(campo: 'fecha_alta',
            imp_origen_id: $imp_destino['imp_origen_id'], limit: 1, link: $link, name_model: $name_model);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener rows',data:  $rows);
        }

        $ejecuciones = $this->ejecuta_inserciones(imp_database_id: $imp_destino['imp_database_id'], link: $link,
            name_model: $name_model, rows: $rows, usuario_id: $usuario_id);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al insertar registros',data:  $ejecuciones);
        }
        return $ejecuciones;
    }

    final public function aplica_inserciones_ultimas_id(array $imp_destino, PDO $link, int $usuario_id){
        $name_model = (new _namespace())->name_model(imp_destino: $imp_destino);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener name_modelo',data:  $name_model);
        }

        $rows = (new _modelado())->rows_origen_ultimos(campo: 'id',
            imp_origen_id: $imp_destino['imp_origen_id'], limit: 1, link: $link, name_model: $name_model);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener rows',data:  $rows);
        }

        $ejecuciones = $this->ejecuta_inserciones(imp_database_id: $imp_destino['imp_database_id'], link: $link,
            name_model: $name_model, rows: $rows, usuario_id: $usuario_id);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al insertar registros',data:  $ejecuciones);
        }
        return $ejecuciones;
    }

    private function ejecuta_insercion_destino(modelo $destino, array $row): array|stdClass
    {
        $data = (new _data())->data_inserta_destino(destino: $destino,row:  $row);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener datos',data:  $data);
        }
        $alta = new stdClass();
        $alta->data_parents = new stdClass();
        $alta->data_parents->existe_parent =false;
        $alta->data_parents->aplica_parents =true;
        if(!$data->existe) {
            $alta = $this->inserta_destino(destino: $destino,row:  $data->row);
            if(errores::$error){
                return $this->error->error(mensaje: 'Error al insertar registro',data:  $alta);
            }
        }

        $retorno = new stdClass();
        $retorno->data = $data;
        $retorno->alta = $alta;



        return $retorno;
    }

    private function ejecuta_inserciones(int $imp_database_id,PDO $link, string $name_model, array $rows, int $usuario_id){
        $destino = (new _modelado())->destino(imp_database_id: $imp_database_id, link: $link, name_model: $name_model);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al conectar con destino',data:  $destino);
        }
        $destino->usuario_id = $usuario_id;

        $ejecuciones = $this->inserta_destinos(destino: $destino, rows: $rows);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al insertar registros',data:  $ejecuciones);
        }
        return $ejecuciones;
    }

    /**
     * rev
     * @param modelo $destino
     * @param array $row
     * @return array|stdClass
     */
    public function inserta_destino(modelo $destino, array $row): array|stdClass
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
        $alta = $destino->alta_registro(registro: $row);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al insertar registro',data:  $alta);
        }
        $retorno->alta = $alta;
        return $retorno;

    }

    private function inserta_destinos(modelo $destino, array $rows){
        $ultimo_id_origen = 0;
        $ejecuciones = array();
        foreach ($rows as $row){
            $ultimo_id_origen = $row['id'];
            $exe = $this->ejecuta_insercion_destino(destino: $destino,row:  $row);
            if(errores::$error){
                return $this->error->error(mensaje: 'Error al insertar registro',data:  $exe);
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
