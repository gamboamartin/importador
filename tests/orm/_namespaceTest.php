<?php
namespace gamboamartin\importador\tests\orm;


use gamboamartin\errores\errores;
use gamboamartin\importador\models\_namespace;
use gamboamartin\importador\models\imp_ultimo;

use gamboamartin\importador\tests\base_test;

use gamboamartin\test\test;
use stdClass;



class _namespaceTest extends test {
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

    public function test_name_model(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'adm_menu';
        $_GET['accion'] = 'lista';
        $_GET['registro_id'] = 1;
        $_SESSION['grupo_id'] = 2;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';



        $nm = new _namespace();
        //$modelo = new liberator($modelo);

        $imp_destino = array();
        $imp_destino['adm_seccion_descripcion'] = 'a';
        $imp_destino['adm_namespace_name'] = 'a';


        $resultado = $nm->name_model($imp_destino);

        $this->assertIsString($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals('\a\models\a',$resultado);

        errores::$error = false;

    }


}

