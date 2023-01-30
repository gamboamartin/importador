<?php
namespace gamboamartin\importador\models;
use base\orm\_modelo_parent;
use PDO;

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


        $columnas_extra = array();
        $tipo_campos = array();


        parent::__construct(link: $link, tabla: $tabla, campos_obligatorios: $campos_obligatorios,
            columnas: $columnas, columnas_extra: $columnas_extra, tipo_campos: $tipo_campos);

        $this->NAMESPACE = __NAMESPACE__;
    }


}