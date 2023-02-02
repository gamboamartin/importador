<?php

namespace gamboamartin\importador\controllers;

use gamboamartin\errores\errores;
use gamboamartin\importador\html\adm_tipo_dato_html;
use gamboamartin\importador\models\adm_tipo_dato;
use gamboamartin\system\_ctl_base;
use gamboamartin\system\links_menu;
use gamboamartin\template_1\html;
use html\adm_seccion_html;
use PDO;
use stdClass;

class controlador_adm_tipo_dato extends _ctl_base {

    private adm_tipo_dato_html $html_local;
    public string $link_adm_campo_alta_bd = '';

    public function __construct(PDO $link, html $html = new html(), array $datatables_custom_cols = array(),
                                array $datatables_custom_cols_omite = array(), stdClass $paths_conf = new stdClass()){
        $modelo = new adm_tipo_dato(link: $link);

        $html_ = new adm_tipo_dato_html(html: $html);
        $this->html_local = $html_;
        $obj_link = new links_menu(link: $link, registro_id: $this->registro_id);



        $datatables = new stdClass();
        $datatables->columns = array();
        $datatables->columns['adm_tipo_dato_id']['titulo'] = 'Id';
        $datatables->columns['adm_tipo_dato_descripcion']['titulo'] = 'Tipo Dato';
        $datatables->columns['adm_tipo_dato_n_campos']['titulo'] = 'N Campos';

        parent::__construct(html: $html_, link: $link, modelo: $modelo, obj_link: $obj_link,
            datatables_custom_cols: $datatables_custom_cols,
            datatables_custom_cols_omite: $datatables_custom_cols_omite, datatables: $datatables,
            paths_conf: $paths_conf);



        $this->titulo_lista = 'Tipos de datos';

        $link_adm_campo_alta_bd = $this->obj_link->link_alta_bd(link: $link, seccion: 'adm_campo');
        if(errores::$error){
            $error = $this->errores->error(mensaje: 'Error al obtener link',data:  $link_adm_campo_alta_bd);
            print_r($error);
            exit;
        }
        $this->link_adm_campo_alta_bd = $link_adm_campo_alta_bd;


        $this->lista_get_data = true;

    }
    final public function alta(bool $header, bool $ws = false): array|string
    {

        $r_alta = $this->init_alta();
        if(errores::$error){
            return $this->retorno_error(
                mensaje: 'Error al inicializar alta',data:  $r_alta, header: $header,ws:  $ws);
        }


        $keys_selects['descripcion'] = new stdClass();
        $keys_selects['descripcion']->cols = 12;

        $inputs = $this->inputs(keys_selects: $keys_selects);
        if(errores::$error){
            return $this->retorno_error(
                mensaje: 'Error al obtener inputs',data:  $inputs, header: $header,ws:  $ws);
        }


        return $r_alta;
    }

    final public function campos(bool $header = true, bool $ws = false): array|stdClass|string
    {

        $data_view = new stdClass();
        $data_view->names = array('Id','Campo', 'Tipo Dato','Seccion','Acciones');
        $data_view->keys_data = array('adm_campo_id','adm_campo_descripcion','adm_tipo_dato_descripcion','adm_seccion_descripcion');
        $data_view->key_actions = 'acciones';
        $data_view->namespace_model = 'gamboamartin\\administrador\\models';
        $data_view->name_model_children = 'adm_campo';


        $contenido_table = $this->contenido_children(data_view: $data_view, next_accion: __FUNCTION__, not_actions: $this->not_actions);
        if(errores::$error){
            return $this->retorno_error(
                mensaje: 'Error al obtener tbody',data:  $contenido_table, header: $header,ws:  $ws);
        }


        return $contenido_table;

    }

    final protected function campos_view(): array
    {
        $keys = new stdClass();
        $keys->inputs = array('codigo','descripcion');
        $keys->selects = array();

        $init_data = array();

        $campos_view = $this->campos_view_base(init_data: $init_data,keys:  $keys);

        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al inicializar campo view',data:  $campos_view);
        }


        return $campos_view;
    }

    final public function get_adm_tipo_dato(bool $header, bool $ws = true): array|stdClass
    {

        $keys['adm_tipo_dato'] = array('id','descripcion','codigo');

        $salida = $this->get_out(header: $header,keys: $keys, ws: $ws);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al generar salida',data:  $salida,header: $header,ws: $ws);

        }

        return $salida;

    }

    protected function inputs_children(stdClass $registro): array|stdClass{

        $select_adm_tipo_dato_id = (new adm_tipo_dato_html(html: $this->html_base))->select_adm_tipo_dato_id(
            cols:12,con_registros: true,id_selected:  $this->registro_id,link:  $this->link, disabled: true);

        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al obtener select_adm_tipo_dato_id',data:  $select_adm_tipo_dato_id);
        }

        $select_adm_seccion_id = (new adm_seccion_html(html: $this->html_base))->select_adm_seccion_id(
            cols:12,con_registros: true,id_selected:  -1,link:  $this->link, disabled: false);

        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al obtener select_adm_seccion_id',data:  $select_adm_seccion_id);
        }

        $adm_campo_descripcion = $this->html_local->input_descripcion(cols: 12, row_upd: new stdClass(),value_vacio: false);
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al obtener adm_campo_descripcion',data:  $adm_campo_descripcion);
        }

        $this->inputs = new stdClass();
        $this->inputs->adm_tipo_dato_id = $select_adm_tipo_dato_id;
        $this->inputs->adm_seccion_id = $select_adm_seccion_id;
        $this->inputs->adm_campo_descripcion = $adm_campo_descripcion;

        return $this->inputs;
    }



    protected function key_selects_txt(array $keys_selects): array
    {

        $keys_selects = (new \base\controller\init())->key_select_txt(cols: 6, key: 'codigo', keys_selects: $keys_selects, place_holder: 'Cod');
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }

        $keys_selects = (new \base\controller\init())->key_select_txt(cols: 12,key: 'descripcion', keys_selects:$keys_selects, place_holder: 'Tipo Dato');
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        return $keys_selects;
    }

    public function modifica(
        bool $header, bool $ws = false): array|stdClass
    {
        $this->not_actions[] = __FUNCTION__;
        $r_modifica = $this->init_modifica(); // TODO: Change the autogenerated stub
        if(errores::$error){
            return $this->retorno_error(
                mensaje: 'Error al generar salida de template',data:  $r_modifica,header: $header,ws: $ws);
        }



        $keys_selects['descripcion'] = new stdClass();
        $keys_selects['descripcion']->cols = 12;

        $keys_selects['codigo'] = new stdClass();
        $keys_selects['codigo']->disabled = true;

        $base = $this->base_upd(keys_selects: $keys_selects, params: array(),params_ajustados: array());
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al integrar base',data:  $base, header: $header,ws:  $ws);
        }

        return $r_modifica;
    }


}