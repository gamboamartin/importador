<?php
namespace gamboamartin\importador\models;
use base\orm\_modelo_parent;
use gamboamartin\errores\errores;
use PDO;
use stdClass;

class imp_server extends _modelo_parent{

    public function __construct(PDO $link){
        $tabla = 'imp_server';
        $columnas = array($tabla=>false);
        $campos_obligatorios[] = 'descripcion';
        $campos_obligatorios[] = 'descripcion_select';
        $campos_obligatorios[] = 'ip';
        $campos_obligatorios[] = 'proveedor';
        $campos_obligatorios[] = 'user';
        $campos_obligatorios[] = 'password';
        $campos_obligatorios[] = 'domain';


        $columnas_extra['imp_server_n_databases'] = /** @lang sql */
            "(SELECT COUNT(*) FROM imp_database WHERE imp_database.imp_server_id = imp_server.id)";
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

    
    private function descripcion_select(array $registro): array|string
    {

        $descripcion_select = $registro['domain'].' '.$registro['ip'];

        $registro['descripcion_select'] = $descripcion_select;
        return $registro;
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