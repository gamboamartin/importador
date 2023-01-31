<?php
namespace gamboamartin\importador\models;
use base\orm\_modelo_parent;
use gamboamartin\administrador\models\adm_seccion;
use gamboamartin\errores\errores;
use PDO;
use stdClass;

class imp_origen extends _modelo_parent{

    public function __construct(PDO $link){
        $tabla = 'imp_origen';
        $columnas = array($tabla=>false,'imp_database'=>$tabla,'imp_server'=>'imp_database','adm_seccion'=>$tabla,
            'adm_menu'=>'adm_seccion');
        $campos_obligatorios[] = 'descripcion';
        $campos_obligatorios[] = 'descripcion_select';
        $campos_obligatorios[] = 'imp_database_id';
        $campos_obligatorios[] = 'adm_seccion_id';



        $columnas_extra = array();
        $tipo_campos = array();


        parent::__construct(link: $link, tabla: $tabla, campos_obligatorios: $campos_obligatorios,
            columnas: $columnas, columnas_extra: $columnas_extra, tipo_campos: $tipo_campos);

        $this->NAMESPACE = __NAMESPACE__;
    }

    public function alta_bd(array $keys_integra_ds = array('codigo', 'descripcion')): array|stdClass
    {
        if(!isset($this->registro['descripcion'])){
            $registro = $this->descripcion(registro: $this->registro);
            if(errores::$error){
                return $this->error->error(mensaje: 'Error asignar descripcion', data: $registro);
            }
            $this->registro = $registro;
        }
        $r_alta_bd = parent::alta_bd(keys_integra_ds: $keys_integra_ds); // TODO: Change the autogenerated stub
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al dar de alta', data: $r_alta_bd);
        }
        return $r_alta_bd;
    }


    private function descripcion(array $registro){
        $imp_database = (new imp_database(link: $this->link))->registro(registro_id: $registro['imp_database_id']);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener imp_database', data: $imp_database);
        }
        $adm_seccion = (new adm_seccion(link: $this->link))->registro(registro_id: $registro['adm_seccion_id']);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener adm_seccion', data: $adm_seccion);
        }

        $descripcion = $adm_seccion['adm_seccion_descripcion'].' '.$imp_database['imp_database_descripcion'];
        $descripcion .= ' '.$imp_database['imp_server_ip'];

        $registro['descripcion'] = $descripcion;
        return $registro;
    }

    public function modifica_bd(array $registro, int $id, bool $reactiva = false, array $keys_integra_ds = array('codigo', 'descripcion')): array|stdClass
    {
        if(!isset($registro['descripcion'])){
            $registro = $this->descripcion(registro: $registro);
            if(errores::$error){
                return $this->error->error(mensaje: 'Error asignar descripcion', data: $registro);
            }
        }

        $r_modifica_bd= parent::modifica_bd(registro: $registro, id: $id,reactiva:  $reactiva,keys_integra_ds:  $keys_integra_ds); // TODO: Change the autogenerated stub
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al modificar', data: $r_modifica_bd);
        }
        return $r_modifica_bd;

    }


}