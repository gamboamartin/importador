<?php
namespace gamboamartin\importador\models;
use base\orm\_modelo_parent;
use gamboamartin\errores\errores;
use PDO;
use stdClass;

class imp_database extends _modelo_parent{

    public function __construct(PDO $link){
        $tabla = 'imp_database';
        $columnas = array($tabla=>false,'imp_server'=>$tabla);
        $campos_obligatorios[] = 'descripcion';
        $campos_obligatorios[] = 'descripcion_select';
        $campos_obligatorios[] = 'imp_server_id';
        $campos_obligatorios[] = 'user';
        $campos_obligatorios[] = 'password';
        $campos_obligatorios[] = 'tipo';


        $columnas_extra['imp_database_n_origenes'] = /** @lang sql */
            "(SELECT COUNT(*) FROM imp_origen WHERE imp_origen.imp_database_id = imp_database.id)";

        $columnas_extra['imp_database_n_destinos'] = /** @lang sql */
            "(SELECT COUNT(*) FROM imp_destino WHERE imp_destino.imp_database_id = imp_database.id)";


        $tipo_campos = array();


        parent::__construct(link: $link, tabla: $tabla, campos_obligatorios: $campos_obligatorios,
            columnas: $columnas, columnas_extra: $columnas_extra, tipo_campos: $tipo_campos);

        $this->NAMESPACE = __NAMESPACE__;
    }

    public function alta_bd(array $keys_integra_ds = array('codigo', 'descripcion')): array|stdClass
    {
        if(!isset($this->registro['descripcion_select'])){
            $registro = $this->descripcion_select(registro: $this->registro);
            if(errores::$error){
                return $this->error->error(mensaje: 'Error al obtener descripcion_select', data: $registro);
            }
            $this->registro = $registro;

        }
        $r_alta_bd = parent::alta_bd($keys_integra_ds); // TODO: Change the autogenerated stub
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al dar de alta', data: $r_alta_bd);
        }
        return $r_alta_bd;
    }

    final public function alta_full(int $imp_database_id){
        $out = array();
        $imp_destinos = $this->destinos(imp_database_id: $imp_database_id);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener destinos',data:  $imp_destinos);
        }

        foreach ($imp_destinos as $imp_destino){
            $r_alta_full = (new imp_destino(link: $this->link))->alta_full(imp_destino_id: $imp_destino['imp_destino_id']);
            if(errores::$error){
                return $this->error->error(mensaje: 'Error al insertar destinos',data:  $r_alta_full);
            }
            $data_out = new stdClass();

            $data_out->r_alta_full = $r_alta_full;
            $data_out->imp_destino_id = $imp_destino['imp_destino_id'];
            $data_out->imp_destino_descripcion = $imp_destino['imp_destino_descripcion'];

            $out[] = $data_out;

        }
        return $out;
    }



    /**
     * Integra la descripcion select de la entidad
     * @param array $registro Registro en proceso
     * @return array|string
     */
    private function descripcion_select(array $registro): array|string
    {
        $imp_server = (new imp_server(link: $this->link))->registro(registro_id: $registro['imp_server_id']);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener imp server', data: $imp_server);
        }

        $descripcion_select = $registro['descripcion'].' '.$imp_server['imp_server_domain'].' '.$imp_server['imp_server_ip'];

        $registro['descripcion_select'] = $descripcion_select;
        return $registro;
    }

    private function destinos(int $imp_database_id){

        $filtro['imp_database.id'] = $imp_database_id;
        $r_imp_destinos = (new imp_destino(link: $this->link))->filtro_and(filtro: $filtro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener destinos', data: $r_imp_destinos);
        }
        return $r_imp_destinos->registros;

    }

    final public function inserta_ultimos(int $imp_database_id){
        $r_altas_full = array();
        $imp_destinos = $this->destinos(imp_database_id: $imp_database_id);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener destinos',data:  $imp_destinos);
        }
        foreach ($imp_destinos as $imp_destino){
            $r_alta_full = (new imp_destino(link: $this->link))->inserta_ultimos(imp_destino_id: $imp_destino['imp_destino_id']);
            if(errores::$error){
                return $this->error->error(mensaje: 'Error al insertar destinos',data:  $r_alta_full);
            }
            $r_altas_full[] = $r_alta_full;
        }
        return $r_altas_full;
    }

    final public function inserta_ultimos_id(int $imp_database_id){
        $r_altas_full = array();
        $imp_destinos = $this->destinos(imp_database_id: $imp_database_id);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener destinos',data:  $imp_destinos);
        }
        foreach ($imp_destinos as $imp_destino){
            $r_alta_full = (new imp_destino(link: $this->link))->inserta_ultimos_id(imp_destino_id: $imp_destino['imp_destino_id']);
            if(errores::$error){
                return $this->error->error(mensaje: 'Error al insertar destinos',data:  $r_alta_full);
            }
            $r_altas_full[] = $r_alta_full;
        }
        return $r_altas_full;
    }

    public function modifica_bd(array $registro, int $id, bool $reactiva = false, array $keys_integra_ds = array('codigo', 'descripcion')): array|stdClass
    {
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