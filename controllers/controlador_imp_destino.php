<?php
/**
 * @author Martin Gamboa Vazquez
 * @version 1.0.0
 * @created 2022-05-14
 * @final En proceso
 *
 */
namespace gamboamartin\importador\controllers;

use gamboamartin\errores\errores;
use gamboamartin\importador\html\imp_destino_html;
use gamboamartin\importador\models\imp_destino;
use gamboamartin\system\_ctl_parent_sin_codigo;
use gamboamartin\system\links_menu;
use gamboamartin\template_1\html;
use PDO;
use stdClass;


class controlador_imp_destino extends _ctl_parent_sin_codigo {

    public stdClass|array $imp_destino = array();
    private imp_destino_html $html_local;

    public string $link_imp_origen_alta_bd = '';


    public function __construct(PDO $link, html $html = new html(), stdClass $paths_conf = new stdClass()){
        $modelo = new imp_destino(link: $link);

        $html_ = new imp_destino_html(html: $html);
        $this->html_local = $html_;
        $obj_link = new links_menu(link: $link, registro_id: $this->registro_id);

        $datatables = new stdClass();
        $datatables->columns = array();
        $datatables->columns['imp_destino_id']['titulo'] = 'Id';
        $datatables->columns['imp_destino_descripcion']['titulo'] = 'Destino';
        $datatables->columns['imp_database_descripcion']['titulo'] = 'Database Origen';

        $datatables->filtro = array();
        $datatables->filtro[] = 'imp_destino.id';
        $datatables->filtro[] = 'imp_destino.descripcion';
        $datatables->filtro[] = 'imp_database.descripcion';


        parent::__construct(html: $html_, link: $link, modelo: $modelo, obj_link: $obj_link, datatables: $datatables,
            paths_conf: $paths_conf);

        $this->titulo_lista = 'Destinos';

        if(isset($this->registro_id) && $this->registro_id > 0){
            $imp_destino = (new imp_destino($this->link))->registro(registro_id: $this->registro_id);
            if(errores::$error){
                $error = $this->errores->error(mensaje: 'Error al obtener imp_destino',data:  $imp_destino);
                print_r($error);
                exit;
            }
            $this->imp_destino = $imp_destino;
        }


    }

    public function alta(bool $header, bool $ws = false): array|string
    {
        $r_alta =  parent::alta($header, $ws); // TODO: Change the autogenerated stub
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al generar template',data:  $r_alta, header: $header,ws: $ws);
        }

        $keys_selects = $this->key_select(cols:6, con_registros: true,filtro:  array(), key: 'imp_origen_id',
            keys_selects: array(), id_selected: -1, label: 'Origen');
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects, header: $header,ws:  $ws);
        }

        $keys_selects = $this->key_select(cols:6, con_registros: true,filtro:  array(), key: 'imp_database_id',
            keys_selects: $keys_selects, id_selected: -1, label: 'Destino');
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects, header: $header,ws:  $ws);
        }

        $inputs = $this->inputs(keys_selects: $keys_selects);
        if(errores::$error){
            return $this->retorno_error(
                mensaje: 'Error al obtener inputs',data:  $inputs, header: $header,ws:  $ws);
        }


        return $r_alta;

    }

    public function alta_full(bool $header, bool $ws = false){

        $r_imp_destino = (new imp_destino(link: $this->link))->alta_full(imp_destino_id: $this->registro_id);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al modificar destino',data:  $r_imp_destino, header: $header,ws:  $ws);
        }
        print_r($r_imp_destino);exit;

    }

    public function inserta_ultimos(bool $header, bool $ws = false){

        $r_imp_destino = (new imp_destino(link: $this->link))->inserta_ultimos(imp_destino_id: $this->registro_id);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al modificar destino',data:  $r_imp_destino, header: $header,ws:  $ws);
        }
        print_r($r_imp_destino);exit;

    }

    public function inserta_ultimos_id(bool $header, bool $ws = false){

        $r_imp_destino = (new imp_destino(link: $this->link))->inserta_ultimos_id(imp_destino_id: $this->registro_id);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al modificar destino',data:  $r_imp_destino, header: $header,ws:  $ws);
        }
        print_r($r_imp_destino);exit;

    }

    public function modifica_full(bool $header, bool $ws = false){
        $r_imp_destino = (new imp_destino(link: $this->link))->modifica_full(imp_destino_id: $this->registro_id);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al modificar destino',data:  $r_imp_destino, header: $header,ws:  $ws);
        }
        print_r($r_imp_destino);exit;
    }

    public function modifica_ultimos(bool $header, bool $ws = false){
        $r_imp_destino = (new imp_destino(link: $this->link))->modifica_ultimos(imp_destino_id: $this->registro_id);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al modificar destino',data:  $r_imp_destino, header: $header,ws:  $ws);
        }
        print_r($r_imp_destino);exit;
    }
    public function modifica_ultimos_id(bool $header, bool $ws = false){
        $r_imp_destino = (new imp_destino(link: $this->link))->modifica_ultimos_id(imp_destino_id: $this->registro_id);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al modificar destino',data:  $r_imp_destino, header: $header,ws:  $ws);
        }
        print_r($r_imp_destino);exit;
    }




    /**
     * Integra los campos para vistas generales
     * @param array $inputs Inputs precargados
     * @return array
     */
    final protected function campos_view(array $inputs = array()): array
    {
        $keys = new stdClass();
        $keys->inputs = array('codigo','descripcion');
        $keys->selects = array();

        $init_data = array();
        $init_data['imp_origen'] = "gamboamartin\\importador";
        $init_data['imp_database'] = "gamboamartin\\importador";
        $campos_view = $this->campos_view_base(init_data: $init_data,keys:  $keys);

        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al inicializar campo view',data:  $campos_view);
        }

        return $campos_view;
    }




    protected function key_selects_txt(array $keys_selects): array
    {
        $keys_selects = (new \base\controller\init())->key_select_txt(cols: 6,key: 'codigo', keys_selects:$keys_selects, place_holder: 'Cod');
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $keys_selects = (new \base\controller\init())->key_select_txt(cols: 6,key: 'descripcion', keys_selects:$keys_selects, place_holder: 'Database');
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        return $keys_selects;
    }

    public function modifica(bool $header, bool $ws = false, array $keys_selects = array()): array|stdClass
    {
        $this->not_actions[] = __FUNCTION__;
        $r_modifica = $this->init_modifica(); // TODO: Change the autogenerated stub
        if(errores::$error){
            return $this->retorno_error(
                mensaje: 'Error al generar salida de template',data:  $r_modifica,header: $header,ws: $ws);
        }


        $keys_selects = $this->key_select(cols:6, con_registros: true,filtro:  array(), key: 'imp_origen_id',
            keys_selects: array(), id_selected: $this->registro['imp_origen_id'], label: 'Origen');
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects, header: $header,ws:  $ws);
        }

        $keys_selects = $this->key_select(cols:6, con_registros: true,filtro:  array(), key: 'imp_database_id',
            keys_selects: $keys_selects, id_selected: $this->registro['imp_database_id'], label: 'Destino');
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects, header: $header,ws:  $ws);
        }


        $base = $this->base_upd(keys_selects: $keys_selects, params: array(),params_ajustados: array());
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al integrar base',data:  $base, header: $header,ws:  $ws);
        }


        return $r_modifica;
    }


}
