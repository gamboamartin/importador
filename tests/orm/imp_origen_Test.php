<?php
namespace gamboamartin\importador\tests;


use gamboamartin\errores\errores;
use gamboamartin\importador\controllers\controlador_adm_session;
use gamboamartin\importador\models\imp_origen;
use gamboamartin\test\liberator;
use gamboamartin\test\test;

use stdClass;


class imp_origen_Test extends test {
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

    public function test_descripcion(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'adm_menu';
        $_GET['accion'] = 'lista';
        $_GET['registro_id'] = 1;
        $_SESSION['grupo_id'] = 2;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';



        $modelo = new imp_origen(link: $this->link);
        $modelo = new liberator($modelo);

        $registro = array();
        $registro['imp_database_id'] = 1;
        $registro['adm_seccion_id'] = 1;
        $resultado = $modelo->descripcion($registro);
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);

        errores::$error = false;

    }


}

