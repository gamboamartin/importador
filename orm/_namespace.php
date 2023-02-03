<?php
namespace gamboamartin\importador\models;
use base\conexion;
use base\orm\modelo;
use gamboamartin\errores\errores;
use gamboamartin\validacion\validacion;
use PDO;
use stdClass;

class _namespace{

    private errores $error;
    private validacion $valida;
    public function __construct(){
        $this->error = new errores();
        $this->valida = new validacion();
    }

    /**
     * Integra el nombre de un modelo a ejecutar
     * @param array $imp_destino Registro con datos del origen y destino para importacion
     * @return string|array
     * @version 0.45.0
     */
    final public function name_model(array $imp_destino): string|array
    {
        $keys = array('adm_seccion_descripcion','adm_namespace_name');
        $valida = $this->valida->valida_existencia_keys(keys: $keys,registro:  $imp_destino);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar imp_destino', data:  $valida);
        }

        $entidad = trim($imp_destino['adm_seccion_descripcion']);
        $namespace = trim($imp_destino['adm_namespace_name']);

        $name_model = '\\'.$namespace.'\\models\\'.$entidad;
        return str_replace('/', '\\', $name_model);
    }




}
