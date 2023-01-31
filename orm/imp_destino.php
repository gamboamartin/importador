<?php
namespace gamboamartin\importador\models;
use base\orm\_modelo_parent;
use PDO;

class imp_destino extends _modelo_parent{

    public function __construct(PDO $link){
        $tabla = 'imp_destino';
        $columnas = array($tabla=>false,'imp_origen'=>$tabla,'imp_database'=>$tabla);
        $campos_obligatorios[] = 'descripcion';
        $campos_obligatorios[] = 'descripcion_select';
        $campos_obligatorios[] = 'imp_origen_id';
        $campos_obligatorios[] = 'imp_database_id';
        $campos_obligatorios[] = 'ultimo_id_origen';
        $campos_obligatorios[] = 'fecha_ultima_ejecucion';


        $columnas_extra = array();

        $tipo_campos = array();


        parent::__construct(link: $link, tabla: $tabla, campos_obligatorios: $campos_obligatorios,
            columnas: $columnas, columnas_extra: $columnas_extra, tipo_campos: $tipo_campos);

        $this->NAMESPACE = __NAMESPACE__;
    }


}