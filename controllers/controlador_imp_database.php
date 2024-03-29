<?php
/**
 * @author Martin Gamboa Vazquez
 * @version 1.0.0
 * @created 2022-05-14
 * @final En proceso
 *
 */
namespace gamboamartin\importador\controllers;


use base\orm\estructuras;
use gamboamartin\comercial\models\com_tmp_cte_dp;
use gamboamartin\errores\errores;
use gamboamartin\importador\html\imp_database_html;
use gamboamartin\importador\html\imp_origen_html;
use gamboamartin\importador\models\_conexion;
use gamboamartin\importador\models\imp_database;
use gamboamartin\system\_ctl_parent_sin_codigo;
use gamboamartin\system\links_menu;
use gamboamartin\template_1\html;
use html\adm_seccion_html;
use PDO;
use stdClass;


class controlador_imp_database extends _ctl_parent_sin_codigo {

    public stdClass|array $imp_database = array();
    private imp_database_html $html_local;

    public string $link_imp_origen_alta_bd = '';
    public string $link_imp_destino_alta_bd = '';

    public array $com_tmp_cte_dps = array();


    public function __construct(PDO $link, html $html = new html(), stdClass $paths_conf = new stdClass()){


        $modelo = new imp_database(link: $link);

        $html_ = new imp_database_html(html: $html);
        $this->html_local = $html_;
        $obj_link = new links_menu(link: $link, registro_id: $this->registro_id);

        $datatables = new stdClass();
        $datatables->columns = array();
        $datatables->columns['imp_database_id']['titulo'] = 'Id';
        $datatables->columns['imp_database_descripcion']['titulo'] = 'Database';
        $datatables->columns['imp_database_user']['titulo'] = 'User';
        $datatables->columns['imp_database_tipo']['titulo'] = 'Tipo';
        $datatables->columns['imp_server_descripcion']['titulo'] = 'Server';
        $datatables->columns['imp_server_ip']['titulo'] = 'IP';
        $datatables->columns['imp_database_n_origenes']['titulo'] = 'N Origenes';
        $datatables->columns['imp_database_n_destinos']['titulo'] = 'N Destinos';

        $datatables->filtro = array();
        $datatables->filtro[] = 'imp_database.id';
        $datatables->filtro[] = 'imp_database.descripcion';
        $datatables->filtro[] = 'imp_database.user';
        $datatables->filtro[] = 'imp_database.tipo';
        $datatables->filtro[] = 'imp_server.descripcion';
        $datatables->filtro[] = 'imp_server.ip';

        parent::__construct(html: $html_, link: $link, modelo: $modelo, obj_link: $obj_link, datatables: $datatables,
            paths_conf: $paths_conf);

        $this->titulo_lista = 'Databases';

        if(isset($this->registro_id) && $this->registro_id > 0){
            $imp_database = (new imp_database($this->link))->registro(registro_id: $this->registro_id);
            if(errores::$error){
                $error = $this->errores->error(mensaje: 'Error al obtener imp_database',data:  $imp_database);
                print_r($error);
                exit;
            }
            $this->imp_database = $imp_database;
        }

        $link_imp_origen_alta_bd = $this->obj_link->link_alta_bd(link: $link, seccion: 'imp_origen');
        if(errores::$error){
            $error = $this->errores->error(mensaje: 'Error al obtener link',data:  $link_imp_origen_alta_bd);
            print_r($error);
            exit;
        }
        $this->link_imp_origen_alta_bd = $link_imp_origen_alta_bd;

        $link_imp_destino_alta_bd = $this->obj_link->link_alta_bd(link: $link, seccion: 'imp_destino');
        if(errores::$error){
            $error = $this->errores->error(mensaje: 'Error al obtener link',data:  $link_imp_destino_alta_bd);
            print_r($error);
            exit;
        }
        $this->link_imp_destino_alta_bd = $link_imp_destino_alta_bd;




    }

    public function alta(bool $header, bool $ws = false): array|string
    {
        $r_alta =  parent::alta($header, $ws); // TODO: Change the autogenerated stub
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al generar template',data:  $r_alta, header: $header,ws: $ws);
        }

        $keys_selects = $this->key_select(cols:6, con_registros: true,filtro:  array(), key: 'imp_server_id',
            keys_selects: array(), id_selected: -1, label: 'Server');
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
        $r_alta_full = (new imp_database(link: $this->link))->alta_full(imp_database_id: $this->registro_id);
        if(errores::$error){
            return $this->retorno_error(
                mensaje: 'Error al insertar destinos',data:  $r_alta_full, header: $header,ws:  $ws);
        }

        var_dump($r_alta_full);
        exit;
    }

    /**
     * Integra los campos para vistas generales
     * @param array $inputs Inputs precargados
     * @return array
     */
    final protected function campos_view(array $inputs = array()): array
    {
        $keys = new stdClass();
        $keys->inputs = array('codigo','descripcion','user','password','tipo');
        $keys->selects = array();

        $init_data = array();
        $init_data['imp_server'] = "gamboamartin\\importador";
        $campos_view = $this->campos_view_base(init_data: $init_data,keys:  $keys);

        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al inicializar campo view',data:  $campos_view);
        }

        return $campos_view;
    }

    public function destinos(bool $header = true, bool $ws = false): array|stdClass|string
    {

        $data_view = new stdClass();
        $data_view->names = array('Id','Destino', 'DB Destino','IP Destino','Seccion','Acciones');
        $data_view->keys_data = array('imp_destino_id','imp_destino_descripcion','imp_database_descripcion','imp_server_ip',
            'adm_seccion_descripcion');
        $data_view->key_actions = 'acciones';
        $data_view->namespace_model = 'gamboamartin\\importador\\models';
        $data_view->name_model_children = 'imp_destino';


        $contenido_table = $this->contenido_children(data_view: $data_view, next_accion: __FUNCTION__, not_actions: $this->not_actions);
        if(errores::$error){
            return $this->retorno_error(
                mensaje: 'Error al obtener tbody',data:  $contenido_table, header: $header,ws:  $ws);
        }


        return $contenido_table;

    }

    protected function inputs_children(stdClass $registro): array|stdClass{
        $select_imp_database_id = (new imp_database_html(html: $this->html_base))->select_imp_database_id(
            cols:12,con_registros: true,id_selected:  $registro->imp_database_id,link:  $this->link, disabled: true);

        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al obtener select_imp_database_id',data:  $select_imp_database_id);
        }

        $select_adm_seccion_id = (new adm_seccion_html(html: $this->html_base))->select_adm_seccion_id(
            cols:12,con_registros: true,id_selected:  -1,link:  $this->link, disabled: false);

        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al obtener select_imp_database_id',data:  $select_adm_seccion_id);
        }

        $select_imp_origen_id = (new imp_origen_html(html: $this->html_base))->select_imp_origen_id(
            cols:12,con_registros: true,id_selected:  -1,link:  $this->link, disabled: false);

        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al obtener select_imp_origen_id',data:  $select_imp_origen_id);
        }




        $this->inputs = new stdClass();
        $this->inputs->imp_database_id = $select_imp_database_id;
        $this->inputs->adm_seccion_id = $select_adm_seccion_id;
        $this->inputs->imp_origen_id = $select_imp_origen_id;

        return $this->inputs;
    }

    public function inserta_ultimos(bool $header, bool $ws = false){
        $r_alta_full = (new imp_database(link: $this->link))->inserta_ultimos(imp_database_id: $this->registro_id);
        if(errores::$error){
            return $this->retorno_error(
                mensaje: 'Error al insertar destinos',data:  $r_alta_full, header: $header,ws:  $ws);
        }

        var_dump($r_alta_full);
        exit;
    }

    public function inserta_ultimos_id(bool $header, bool $ws = false){
        $r_alta_full = (new imp_database(link: $this->link))->inserta_ultimos_id(imp_database_id: $this->registro_id);
        if(errores::$error){
            return $this->retorno_error(
                mensaje: 'Error al insertar destinos',data:  $r_alta_full, header: $header,ws:  $ws);
        }

        var_dump($r_alta_full);
        exit;
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

        $keys_selects = (new \base\controller\init())->key_select_txt(cols: 6,key: 'user', keys_selects:$keys_selects, place_holder: 'User');
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }
        $keys_selects = (new \base\controller\init())->key_select_txt(cols: 6,key: 'password', keys_selects:$keys_selects, place_holder: 'Password');
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }
        $keys_selects = (new \base\controller\init())->key_select_txt(cols: 12,key: 'tipo', keys_selects:$keys_selects, place_holder: 'Tipo');
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


        $keys_selects = $this->key_select(cols:6, con_registros: true,filtro:  array(), key: 'imp_server_id',
            keys_selects: array(), id_selected: $this->registro['imp_server_id'], label: 'Server');
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects, header: $header,ws:  $ws);
        }


        $base = $this->base_upd(keys_selects: $keys_selects, params: array(),params_ajustados: array());
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al integrar base',data:  $base, header: $header,ws:  $ws);
        }




        return $r_modifica;
    }

    public function origenes(bool $header = true, bool $ws = false): array|stdClass|string
    {

        $contenido_table = (new _base_importador())->origenes(controler: $this,next_accion: __FUNCTION__);
        if(errores::$error){
            return $this->retorno_error(
                mensaje: 'Error al obtener tbody',data:  $contenido_table, header: $header,ws:  $ws);
        }

        return $contenido_table;

    }

    public function regenera_dom_cte(bool $header = true, bool $ws = false){

        $imp_database = (new imp_database(link: $this->link))->registro(registro_id: $this->registro_id);
        if(errores::$error){

            $error = $this->errores->error(mensaje: 'Error al obtener imp_database',data:  $imp_database);
            print_r($error);
            exit;
        }

        $link_destino = (new _conexion())->link_destino(imp_database_id: $this->registro_id,link:  $this->link);
        if(errores::$error){

            $error = $this->errores->error(mensaje: 'Error al conectar imp_database',data:  $link_destino);
            print_r($error);
            exit;
        }

        $name_db = $imp_database['imp_database_descripcion'];

        $estructura = (new estructuras(link: $link_destino));
        $entidades = $estructura->entidades(name_db: $name_db);
        if(errores::$error){

            $error =  $this->errores->error(mensaje: 'Error al obtener entidades',data:  $entidades);
            print_r($error);
            exit;
        }
        if(!in_array('com_tmp_cte_dp', $entidades)){
            echo 'No aplica';
            exit;
        }


        $com_tmp_cte_dp_modelo = (new com_tmp_cte_dp(link: $link_destino));

        $com_tmp_cte_dps = $com_tmp_cte_dp_modelo->registros();
        if(errores::$error){
            $error =  $this->errores->error(mensaje: 'Error al obtener temporales',data:  $com_tmp_cte_dps);
            print_r($error);
            exit;
        }

        foreach ($com_tmp_cte_dps as $com_tmp_cte_dp){
            $link_destino->beginTransaction();
            $regenera = $com_tmp_cte_dp_modelo->regenera(com_tmp_cte_dp_id: $com_tmp_cte_dp['com_tmp_cte_dp_id']);
            if(errores::$error){
                $link_destino->rollBack();
                $error =  $this->errores->error(mensaje: 'Error al regenerar',data:  $regenera);
                print_r($error);
                exit;
            }
            $link_destino->commit();
        }

        $this->com_tmp_cte_dps = $com_tmp_cte_dps;

    }



}
