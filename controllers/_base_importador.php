<?php

namespace gamboamartin\importador\controllers;
use gamboamartin\errores\errores;
use gamboamartin\system\_ctl_base;
use stdClass;

class _base_importador{
    private errores $error;

    public function __construct(){
        $this->error = new errores();
    }

    final public function campos(_ctl_base $controler, string $next_accion): array|stdClass|string
    {

        $data_view = new stdClass();
        $data_view->names = array('Id','Campo', 'Tipo Dato','Seccion','Acciones');
        $data_view->keys_data = array('adm_campo_id','adm_campo_descripcion','adm_tipo_dato_descripcion','adm_seccion_descripcion');
        $data_view->key_actions = 'acciones';
        $data_view->namespace_model = 'gamboamartin\\administrador\\models';
        $data_view->name_model_children = 'adm_campo';


        $contenido_table = $controler->contenido_children(data_view: $data_view, next_accion: $next_accion, not_actions: $controler->not_actions);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener tbody',data:  $contenido_table);
        }


        return $contenido_table;

    }

    public function origenes(_ctl_base $controler, string $next_accion): array|stdClass|string
    {

        $data_view = new stdClass();
        $data_view->names = array('Id','Origen', 'DB','IP','Seccion','Acciones');
        $data_view->keys_data = array('imp_origen_id','imp_origen_descripcion','imp_database_descripcion',
            'imp_server_ip','adm_seccion_descripcion');
        $data_view->key_actions = 'acciones';
        $data_view->namespace_model = 'gamboamartin\\importador\\models';
        $data_view->name_model_children = 'imp_origen';


        $contenido_table = $controler->contenido_children(data_view: $data_view, next_accion: $next_accion, not_actions: $controler->not_actions);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener tbody',data:  $contenido_table);
        }

        return $contenido_table;

    }
}
