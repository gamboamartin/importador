<?php
namespace gamboamartin\importador\models;
use base\orm\_modelo_parent;
use PDO;

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


        $tipo_campos = array();


        parent::__construct(link: $link, tabla: $tabla, campos_obligatorios: $campos_obligatorios,
            columnas: $columnas, columnas_extra: $columnas_extra, tipo_campos: $tipo_campos);

        $this->NAMESPACE = __NAMESPACE__;
    }


}