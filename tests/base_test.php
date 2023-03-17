<?php
namespace gamboamartin\importador\tests;
use base\orm\modelo_base;
use gamboamartin\administrador\models\adm_seccion;
use gamboamartin\errores\errores;
use gamboamartin\importador\models\imp_database;
use gamboamartin\importador\models\imp_destino;
use gamboamartin\importador\models\imp_origen;
use gamboamartin\importador\models\imp_server;
use gamboamartin\importador\models\imp_ultimo;
use PDO;


class base_test{

    public function alta_adm_seccion(PDO $link, int $id = 1): array|\stdClass
    {
        $alta = (new \gamboamartin\administrador\tests\base_test())->alta_adm_seccion(link: $link, id: $id);
        if(errores::$error){
            return (new errores())->error('Error al insertar', $alta);
        }

        return $alta;
    }

    public function alta_imp_database(PDO $link, string $descripcion = 'test', int $id = 1, int $imp_server_id = 1,
                                      string $password = 'abc', string $tipo = 'MYSQL', string $user = 'abc'): array|\stdClass
    {

        $existe = (new imp_server(link: $link))->existe_by_id(registro_id: $imp_server_id);
        if(errores::$error){
            return (new errores())->error('Error al validar si existe', $existe);
        }

        if(!$existe){
            $alta = $this->alta_imp_server(link: $link, id: $imp_server_id);
            if(errores::$error){
                return (new errores())->error('Error al al insertar', $alta);
            }
        }


        $registro['id'] = $id;
        $registro['imp_server_id'] = $imp_server_id;
        $registro['user'] = $user;
        $registro['password'] = $password;
        $registro['tipo'] = $tipo;
        $registro['descripcion'] = $descripcion;
        $alta = (new imp_database($link))->alta_registro(registro: $registro);
        if(errores::$error){
            return (new errores())->error('Error al insertar', $alta);
        }

        return $alta;
    }

    public function alta_imp_destino(PDO $link, int $id = 1, int $imp_database_id = 1, int $imp_origen_id = 1): array|\stdClass
    {

        $existe = (new imp_database(link: $link))->existe_by_id(registro_id: $imp_database_id);
        if(errores::$error){
            return (new errores())->error('Error al validar si existe', $existe);
        }

        if(!$existe){
            $alta = $this->alta_imp_database(link: $link,user: $imp_database_id);
            if(errores::$error){
                return (new errores())->error('Error al insertar', $alta);
            }
        }

        $existe = (new imp_origen(link: $link))->existe_by_id(registro_id: $imp_origen_id);
        if(errores::$error){
            return (new errores())->error('Error al validar si existe', $existe);
        }

        if(!$existe){
            $alta = $this->alta_imp_origen(link: $link,imp_database_id: $imp_database_id);
            if(errores::$error){
                return (new errores())->error('Error al insertar', $alta);
            }
        }


        $registro['id'] = $id;
        $registro['imp_origen_id'] = $imp_origen_id;
        $registro['imp_database_id'] = $imp_database_id;

        $alta = (new imp_destino($link))->alta_registro(registro: $registro);
        if(errores::$error){
            return (new errores())->error('Error al insertar', $alta);
        }

        return $alta;
    }

    public function alta_imp_server(PDO $link, int $id = 1, string $descripcion = 'test', string $domain = 'localhost',
                                    string $ip = '127.0.0.1', string $password = 'test', string $proveedor = 'test',
                                    string $user = 'test'): array|\stdClass
    {

        $registro['id'] = $id;
        $registro['domain'] = $domain;
        $registro['ip'] = $ip;
        $registro['password'] = $password;
        $registro['proveedor'] = $proveedor;
        $registro['user'] = $user;
        $registro['descripcion'] = $descripcion;
        $alta = (new imp_server($link))->alta_registro(registro: $registro);
        if(errores::$error){
            return (new errores())->error('Error al insertar', $alta);
        }

        return $alta;
    }

    public function alta_imp_origen(PDO $link, int $adm_seccion_id = 1, int $id = 1, int $imp_database_id = 1): array|\stdClass
    {
        $existe = (new adm_seccion(link: $link))->existe_by_id(registro_id: $adm_seccion_id);
        if(errores::$error){
            return (new errores())->error('Error al validar si existe', $existe);
        }

        if(!$existe){
            $alta = $this->alta_adm_seccion(link: $link,id: $adm_seccion_id);
            if(errores::$error){
                return (new errores())->error('Error al insertar', $alta);
            }
        }

        $registro['id'] = $id;
        $registro['imp_database_id'] = $imp_database_id;
        $registro['adm_seccion_id'] = $adm_seccion_id;
        $alta = (new imp_origen($link))->alta_registro(registro: $registro);
        if(errores::$error){
            return (new errores())->error('Error al insertar', $alta);
        }

        return $alta;
    }

    public function alta_imp_ultimo(PDO $link, int $adm_accion_id = 1, int $id = 1, int $imp_destino_id = 1): array|\stdClass
    {

        $existe = (new imp_destino(link: $link))->existe_by_id(registro_id: $imp_destino_id);
        if(errores::$error){
            return (new errores())->error('Error al validar si existe', $existe);
        }

        if(!$existe){
            $alta = $this->alta_imp_destino($link);
            if(errores::$error){
                return (new errores())->error('Error al insertar', $alta);
            }
        }


        $registro['id'] = $id;
        $registro['imp_destino_id'] = $imp_destino_id;
        $registro['adm_accion_id'] = $adm_accion_id;
        $registro['id_ultimo'] = $adm_accion_id;
        $alta = (new imp_ultimo($link))->alta_registro(registro: $registro);
        if(errores::$error){
            return (new errores())->error('Error al insertar', $alta);
        }

        return $alta;
    }

    public function del(PDO $link, string $name_model): array
    {
        $model = (new modelo_base($link))->genera_modelo(modelo: $name_model);
        $del = $model->elimina_todo();
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al eliminar '.$name_model, data: $del);
        }
        return $del;
    }

    public function del_imp_ultimo(PDO $link): array|\stdClass
    {

        $del = $this->del($link, 'gamboamartin\\importador\\models\\imp_ultimo');
        if(errores::$error){
            return (new errores())->error('Error al eliminar', $del);
        }


        return $del;
    }



}
