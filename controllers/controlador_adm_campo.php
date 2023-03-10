<?php

namespace gamboamartin\importador\controllers;


use gamboamartin\administrador\models\adm_campo;
use gamboamartin\errores\errores;
use gamboamartin\importador\html\adm_campo_html;
use gamboamartin\system\_ctl_base;
use gamboamartin\system\links_menu;
use gamboamartin\template_1\html;
use PDO;
use stdClass;

class controlador_adm_campo extends _ctl_base {


    public function __construct(PDO $link, html $html = new html(), array $datatables_custom_cols = array(),
                                array $datatables_custom_cols_omite = array(), stdClass $paths_conf = new stdClass()){
        $modelo = new adm_campo(link: $link);

        $html_ = new adm_campo_html(html: $html);
        $obj_link = new links_menu(link: $link, registro_id: $this->registro_id);

        $datatables = new stdClass();
        $datatables->columns = array();
        $datatables->columns['adm_campo_id']['titulo'] = 'Id';
        $datatables->columns['adm_campo_descripcion']['titulo'] = 'Campo';
        $datatables->columns['adm_tipo_dato_descripcion']['titulo'] = 'Tipo de dato';
        $datatables->columns['adm_seccion_descripcion']['titulo'] = 'Seccion';

        $datatables->filtro = array();
        $datatables->filtro[] = 'adm_campo.id';
        $datatables->filtro[] = 'adm_campo.descripcion';
        $datatables->filtro[] = 'adm_tipo_dato.descripcion';
        $datatables->filtro[] = 'adm_seccion.descripcion';

        parent::__construct(html: $html_, link: $link, modelo: $modelo, obj_link: $obj_link,
            datatables_custom_cols: $datatables_custom_cols,
            datatables_custom_cols_omite: $datatables_custom_cols_omite, datatables: $datatables,
            paths_conf: $paths_conf);



        $this->titulo_lista = 'Campos';


        $this->lista_get_data = true;

    }


    public function alta(bool $header, bool $ws = false): array|string
    {

        $r_alta = $this->init_alta();
        if(errores::$error){
            return $this->retorno_error(
                mensaje: 'Error al inicializar alta',data:  $r_alta, header: $header,ws:  $ws);
        }

        $keys_selects = $this->key_select(cols:6, con_registros: true,filtro:  array(), key: 'adm_seccion_id',
            keys_selects: array(), id_selected: -1, label: 'Seccion');
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects, header: $header,ws:  $ws);
        }

        $keys_selects = $this->key_select(cols:6, con_registros: true,filtro:  array(), key: 'adm_tipo_dato_id',
            keys_selects: $keys_selects, id_selected: -1, label: 'Tipo Dato');
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects, header: $header,ws:  $ws);
        }

        $keys_selects['descripcion'] = new stdClass();
        $keys_selects['descripcion']->cols = 12;

        $keys_selects['sub_consulta'] = new stdClass();
        $keys_selects['sub_consulta']->cols = 12;

        $inputs = $this->inputs(keys_selects: $keys_selects);
        if(errores::$error){
            return $this->retorno_error(
                mensaje: 'Error al obtener inputs',data:  $inputs, header: $header,ws:  $ws);
        }

        return $r_alta;
    }

    protected function campos_view(): array
    {
        $keys = new stdClass();
        $keys->inputs = array('codigo','descripcion','sub_consulta');
        $keys->selects = array();

        $init_data = array();
        $init_data['adm_seccion'] = "gamboamartin\\administrador";
        $init_data['adm_tipo_dato'] = "gamboamartin\\administrador";

        $campos_view = $this->campos_view_base(init_data: $init_data,keys:  $keys);

        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al inicializar campo view',data:  $campos_view);
        }


        return $campos_view;
    }

    public function get_adm_campo(bool $header, bool $ws = true): array|stdClass
    {

        $keys['adm_tipo_dato'] = array('id','descripcion','codigo');
        $keys['adm_campo'] = array('id','descripcion','codigo');

        $salida = $this->get_out(header: $header,keys: $keys, ws: $ws);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al generar salida',data:  $salida,header: $header,ws: $ws);

        }

        return $salida;

    }



    protected function key_selects_txt(array $keys_selects): array
    {

        $keys_selects = (new \base\controller\init())->key_select_txt(cols: 6, key: 'codigo', keys_selects: $keys_selects, place_holder: 'Cod');
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }

        $keys_selects = (new \base\controller\init())->key_select_txt(cols: 12,key: 'descripcion', keys_selects:$keys_selects, place_holder: 'Campo');
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $keys_selects = (new \base\controller\init())->key_select_txt(cols: 12,key: 'sub_consulta', keys_selects:$keys_selects, place_holder: 'Subconsulta',required: false);
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

        $keys_selects = $this->key_select(cols:6, con_registros: true,filtro:  array(), key: 'adm_seccion_id',
            keys_selects: array(), id_selected: $this->registro['adm_seccion_id'], label: 'Seccion');
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects, header: $header,ws:  $ws);
        }

        $keys_selects = $this->key_select(cols:6, con_registros: true,filtro:  array(), key: 'adm_tipo_dato_id',
            keys_selects: $keys_selects, id_selected: $this->registro['adm_tipo_dato_id'], label: 'Seccion');
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects, header: $header,ws:  $ws);
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