<?php
namespace gamboamartin\importador\models;
use base\conexion;
use base\orm\modelo;
use gamboamartin\errores\errores;
use PDO;
use stdClass;

class _namespace{

    private errores $error;
    public function __construct(){
        $this->error = new errores();
    }

    /**
     * rev
     * @param array $imp_destino
     * @return string
     */
    public function name_model(array $imp_destino): string
    {
        $entidad = trim($imp_destino['adm_seccion_descripcion']);
        $namespace = trim($imp_destino['adm_namespace_name']);

        $name_model = '\\'.$namespace.'\\models\\'.$entidad;
        return str_replace('/', '\\', $name_model);
    }




}
