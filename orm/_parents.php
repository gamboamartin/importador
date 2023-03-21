<?php
namespace gamboamartin\importador\models;
use base\conexion;
use base\orm\modelo;
use gamboamartin\errores\errores;
use gamboamartin\validacion\validacion;
use PDO;
use stdClass;

class _parents{

    private errores $error;
    private validacion $valida;
    public function __construct(){
        $this->error = new errores();
        $this->valida = new validacion();
    }

    /**
     * rev
     * @param modelo $destino
     * @param array $row
     * @return array|stdClass
     */
    final public function data_parents(modelo $destino, array $row): array|stdClass
    {
        $parents = $destino->parents_data;

        $existe_parent = false;
        $aplica_parents = false;

        //print_r($parents);exit;

        foreach ($parents as $model_parent =>$parent){

            $aplica_parents = true;

            $existe_parent = $this->existe_parent(destino: $destino, model_parent: $model_parent, parent: $parent,row:  $row);
            if(errores::$error){
                return $this->error->error(mensaje: 'Error al verificar si existe ',data:  $existe_parent);
            }
            if(!$existe_parent){
                break;
            }
        }

        $data = new stdClass();
        $data->existe_parent = $existe_parent;
        $data->aplica_parents = $aplica_parents;
        return $data;
    }

    /**
     * rev
     * @param modelo $destino
     * @param string $model_parent
     * @param array $parent
     * @param array $row
     * @return bool|array
     */
    private function existe_parent(modelo $destino, string $model_parent, array $parent, array $row): bool|array
    {
        $parent_id = $this->parent_id(parent: $parent, row: $row);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener parent_id',data:  $parent_id);
        }

        $obj_parent = $this->obj_parent(destino: $destino, model_parent: $model_parent, parent: $parent);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al generar objeto',data:  $obj_parent);
        }

        $existe_parent = $obj_parent->existe_by_id(registro_id: $parent_id);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al verificar si existe ',data:  $existe_parent);
        }
        return $existe_parent;
    }

    /**
     * rev
     * @param string $model_parent
     * @param array $parent
     * @return string
     */
    private function name_obj_parent(string $model_parent, array $parent): string
    {
        $namespace_parent = $parent['namespace'];
        return $namespace_parent.'\\'.$model_parent;
    }

    /**
     * rev
     * @param modelo $destino
     * @param string $model_parent
     * @param array $parent
     * @return modelo|array
     */
    private function obj_parent(modelo $destino, string $model_parent, array $parent): modelo|array
    {
        $name_obj_parent = $this->name_obj_parent(model_parent: $model_parent, parent: $parent);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al generar objeto',data:  $name_obj_parent);
        }
        /**
         * @var modelo $name_obj_parent;
         */
        return new $name_obj_parent(link:$destino->link);
    }

    /**
     *
     * @param array $parent
     * @param array $row
     * @return int|array
     */
    final public function parent_id(array $parent, array $row): int|array
    {
        $keys = array('key_id');
        $valida = $this->valida->valida_existencia_keys(keys: $keys,registro:  $parent);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar parent',data:  $valida);
        }


        $key_id_parent = (string)$parent['key_id'];

        $keys = array($key_id_parent);
        $valida = $this->valida->valida_existencia_keys(keys: $keys,registro:  $row);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar row',data:  $valida);
        }

        return $row[$key_id_parent];
    }


}
