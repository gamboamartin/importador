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



        $columnas_extra['imp_origen_n_destinos'] = /** @lang sql */
            "(SELECT COUNT(*) FROM imp_destino WHERE imp_destino.imp_origen_id = imp_origen.id)";
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
        if(!isset($this->registro['descripcion_select'])){
            $registro = $this->descripcion_select(registro: $this->registro);
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

    final public function alta_full(int $imp_origen_id){
        $r_altas_full = array();
        $imp_destinos = $this->destinos(imp_origen_id: $imp_origen_id);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener destinos',data:  $imp_destinos);
        }
        foreach ($imp_destinos as $imp_destino){
            $r_alta_full = (new imp_destino(link: $this->link))->alta_full(imp_destino_id: $imp_destino['imp_destino_id']);
            if(errores::$error){
                return $this->error->error(mensaje: 'Error al insertar destinos',data:  $r_alta_full);
            }
            $r_altas_full[] = $r_alta_full;
        }
        return $r_altas_full;
    }


    /**
     * Genera la descripcion para origen
     * @param array $registro Registro en proceso
     * @return array
     * @version 0.22.0
     */
    private function descripcion(array $registro): array
    {
        $keys = array('imp_database_id','adm_seccion_id');
        $valida = $this->validacion->valida_ids(keys: $keys,registro:  $registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar registro', data: $valida);
        }

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

    private function descripcion_select(array $registro): array|string
    {
        $imp_database = (new imp_database(link: $this->link))->registro(registro_id: $registro['imp_database_id']);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener imp_database', data: $imp_database);
        }

        $adm_seccion = (new adm_seccion(link: $this->link))->registro(registro_id: $registro['adm_seccion_id']);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener adm_seccion', data: $adm_seccion);
        }

        $descripcion_select = $imp_database['imp_server_domain'].' '.$imp_database['imp_server_ip'];
        $descripcion_select .= ' '.$adm_seccion['adm_seccion_descripcion'].' '.$imp_database['imp_database_descripcion'];

        $registro['descripcion_select'] = $descripcion_select;
        return $registro;
    }

    private function destinos(int $imp_origen_id){

        $filtro['imp_origen.id'] = $imp_origen_id;
        $r_imp_destinos = (new imp_destino(link: $this->link))->filtro_and(filtro: $filtro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener destinos', data: $r_imp_destinos);
        }
        return $r_imp_destinos->registros;

    }

    public function modifica_bd(array $registro, int $id, bool $reactiva = false, array $keys_integra_ds = array('codigo', 'descripcion')): array|stdClass
    {
        if(!isset($registro['descripcion'])){
            $registro = $this->descripcion(registro: $registro);
            if(errores::$error){
                return $this->error->error(mensaje: 'Error asignar descripcion', data: $registro);
            }
        }
        if(!isset($registro['descripcion_select'])){
            $registro = $this->descripcion_select(registro: $registro);
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