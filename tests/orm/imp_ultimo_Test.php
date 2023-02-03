<?php
namespace gamboamartin\importador\tests\orm;


use gamboamartin\errores\errores;
use gamboamartin\importador\models\imp_ultimo;

use gamboamartin\importador\tests\base_test;

use gamboamartin\test\test;
use stdClass;



class imp_ultimo_Test extends test {
    public errores $errores;
    private stdClass $paths_conf;
    public function __construct(?string $name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->errores = new errores();
        $this->paths_conf = new stdClass();
        $this->paths_conf->generales = '/var/www/html/acl/config/generales.php';
        $this->paths_conf->database = '/var/www/html/acl/config/database.php';
        $this->paths_conf->views = '/var/www/html/acl/config/views.php';
    }

    public function test_imp_ultimo_by_destino(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'adm_menu';
        $_GET['accion'] = 'lista';
        $_GET['registro_id'] = 1;
        $_SESSION['grupo_id'] = 2;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';



        $modelo = new imp_ultimo(link: $this->link);
        //$modelo = new liberator($modelo);

        $del = (new base_test())->del_imp_ultimo(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje: 'Error al eliminar', data: $del);
            print_r($error);
            exit;
        }

        $alta = (new base_test())->alta_imp_ultimo(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje: 'Error al dar de alta', data: $alta);
            print_r($error);
            exit;
        }

        $adm_accion_descripcion = 'alta';
        $imp_destino_id = 1;

        $resultado = $modelo->imp_ultimo_by_destino($adm_accion_descripcion, $imp_destino_id);

        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals('localhost cat_sat adm_accion',$resultado['imp_ultimo_descripcion']);

        errores::$error = false;

    }


}

