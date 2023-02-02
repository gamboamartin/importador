<?php
namespace gamboamartin\importador\controllers;

use gamboamartin\errores\errores;
use gamboamartin\importador\html\imp_database_html;
use gamboamartin\importador\models\adm_seccion;
use gamboamartin\template_1\html;
use html\adm_seccion_html;
use PDO;
use stdClass;

class controlador_adm_seccion extends \gamboamartin\acl\controllers\controlador_adm_seccion {
    public array $not_actions = array('acciones','elimina_bd','modifica','status');
    public string $link_imp_origen_alta_bd = '';

    public function __construct(PDO $link, html $html = new html(), stdClass $paths_conf = new stdClass())

    {

        $datatables_custom_cols = array();
        $datatables_custom_cols['adm_seccion_n_origenes']['titulo'] = 'N Origenes';
        $datatables_custom_cols_omite[] = 'adm_seccion_n_acciones';

        parent::__construct(link: $link, html: $html, datatables_custom_cols: $datatables_custom_cols,
            datatables_custom_cols_omite: $datatables_custom_cols_omite, paths_conf: $paths_conf);


        $this->modelo = new adm_seccion(link: $link);

        $link_imp_origen_alta_bd = $this->obj_link->link_alta_bd(link: $link, seccion: 'imp_origen');
        if(errores::$error){
            $error = $this->errores->error(mensaje: 'Error al obtener link',data:  $link_imp_origen_alta_bd);
            print_r($error);
            exit;
        }
        $this->link_imp_origen_alta_bd = $link_imp_origen_alta_bd;

    }

    protected function inputs_children(stdClass $registro): array|stdClass{
        $select_imp_database_id = (new imp_database_html(html: $this->html_base))->select_imp_database_id(
            cols:12,con_registros: true,id_selected:  -1,link:  $this->link, disabled: false);

        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al obtener select_imp_database_id',data:  $select_imp_database_id);
        }

        $select_adm_seccion_id = (new adm_seccion_html(html: $this->html_base))->select_adm_seccion_id(
            cols:12,con_registros: true,id_selected:  $registro->adm_seccion_id,link:  $this->link, disabled: true);

        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al obtener select_imp_database_id',data:  $select_adm_seccion_id);
        }

        $this->inputs = new stdClass();
        $this->inputs->imp_database_id = $select_imp_database_id;
        $this->inputs->adm_seccion_id = $select_adm_seccion_id;

        return $this->inputs;
    }

    public function origenes(bool $header = true, bool $ws = false): array|stdClass|string
    {

        $data_view = new stdClass();
        $data_view->names = array('Id','Origen', 'DB','IP','Seccion','Acciones');
        $data_view->keys_data = array('imp_origen_id','imp_origen_descripcion','imp_database_descripcion',
            'imp_server_ip','adm_seccion_descripcion');
        $data_view->key_actions = 'acciones';
        $data_view->namespace_model = 'gamboamartin\\importador\\models';
        $data_view->name_model_children = 'imp_origen';


        $contenido_table = $this->contenido_children(data_view: $data_view, next_accion: __FUNCTION__, not_actions: $this->not_actions);
        if(errores::$error){
            return $this->retorno_error(
                mensaje: 'Error al obtener tbody',data:  $contenido_table, header: $header,ws:  $ws);
        }


        return $contenido_table;

    }
}
