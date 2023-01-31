<?php
namespace gamboamartin\importador\models;
use base\orm\_modelo_parent;
use PDO;

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


}