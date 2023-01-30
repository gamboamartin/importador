<?php
/**
 * @author Martin Gamboa Vazquez
 * @version 1.0.0
 * @created 2022-05-14
 * @final En proceso
 *
 */
namespace gamboamartin\importador\controllers;

use base\controller\init;

use gamboamartin\errores\errores;
use gamboamartin\importador\html\imp_server_html;
use gamboamartin\importador\models\imp_server;
use gamboamartin\system\_ctl_parent_sin_codigo;
use gamboamartin\system\links_menu;
use gamboamartin\template_1\html;
use PDO;
use stdClass;


class controlador_imp_server extends _ctl_parent_sin_codigo {

    public stdClass|array $imp_sever = array();


    public function __construct(PDO $link, html $html = new html(), stdClass $paths_conf = new stdClass()){
        $modelo = new imp_server(link: $link);

        $html_ = new imp_server_html(html: $html);
        $obj_link = new links_menu(link: $link, registro_id: $this->registro_id);

        $datatables = new stdClass();
        $datatables->columns = array();
        $datatables->columns['imp_server_id']['titulo'] = 'Id';
        $datatables->columns['imp_server_descripcion']['titulo'] = 'Server';
        $datatables->columns['imp_server_ip']['titulo'] = 'IP';
        $datatables->columns['imp_server_proveedor']['titulo'] = 'Prov';
        $datatables->columns['imp_server_user']['titulo'] = 'User';
        $datatables->columns['imp_server_password']['titulo'] = 'Pass';
        $datatables->columns['imp_server_domain']['titulo'] = 'Domain';

        $datatables->filtro = array();
        $datatables->filtro[] = 'imp_server.id';
        $datatables->filtro[] = 'imp_server.descripcion';
        $datatables->filtro[] = 'imp_server.ip';
        $datatables->filtro[] = 'imp_server.proveedor';
        $datatables->filtro[] = 'imp_server.user';
        $datatables->filtro[] = 'imp_server.domain';

        parent::__construct(html: $html_, link: $link, modelo: $modelo, obj_link: $obj_link, datatables: $datatables,
            paths_conf: $paths_conf);

        $this->titulo_lista = 'Servidores';

        if(isset($this->registro_id) && $this->registro_id > 0){
            $imp_sever = (new imp_server($this->link))->registro(registro_id: $this->registro_id);
            if(errores::$error){
                $error = $this->errores->error(mensaje: 'Error al obtener imp_sever',data:  $imp_sever);
                print_r($error);
                exit;
            }
            $this->imp_sever = $imp_sever;
        }


    }


    protected function campos_view(array $inputs = array()): array
    {
        $keys = new stdClass();
        $keys->inputs = array('codigo','descripcion','ip','proveedor','user','password','domain');
        $keys->selects = array();

        $campos_view = (new init())->model_init_campos_template(
            campos_view: array(),keys:  $keys, link: $this->link);

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

        $keys_selects = (new \base\controller\init())->key_select_txt(cols: 6,key: 'descripcion', keys_selects:$keys_selects, place_holder: 'Server');
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $keys_selects = (new \base\controller\init())->key_select_txt(cols: 12,key: 'ip', keys_selects:$keys_selects, place_holder: 'IP');
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }
        $keys_selects = (new \base\controller\init())->key_select_txt(cols: 12,key: 'proveedor', keys_selects:$keys_selects, place_holder: 'Proveedor');
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }
        $keys_selects = (new \base\controller\init())->key_select_txt(cols: 12,key: 'user', keys_selects:$keys_selects, place_holder: 'User');
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }
        $keys_selects = (new \base\controller\init())->key_select_txt(cols: 12,key: 'password', keys_selects:$keys_selects, place_holder: 'Password');
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }
        $keys_selects = (new \base\controller\init())->key_select_txt(cols: 12,key: 'domain', keys_selects:$keys_selects, place_holder: 'Domain');
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        return $keys_selects;
    }



}
