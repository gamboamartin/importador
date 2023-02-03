<?php
namespace gamboamartin\importador\models;
use base\conexion;
use gamboamartin\errores\errores;
use PDO;
use stdClass;

class _conexion{

    private errores $error;
    public function __construct(){
        $this->error = new errores();
    }

    /**
     * Genera la configuracion de una base de datos
     * @param stdClass $data_cnx Datos de conexion
     * @return stdClass
     */
    private function conf_database(stdClass $data_cnx): stdClass
    {
        $conf_database = new stdClass();
        $conf_database->db_host = $data_cnx->imp_server_domain;
        $conf_database->db_name = $data_cnx->imp_database_descripcion;
        $conf_database->db_user = $data_cnx->imp_database_user;
        $conf_database->db_password = $data_cnx->imp_database_password;
        $conf_database->set_name = 'utf8';
        $conf_database->time_out = 10;
        $conf_database->sql_mode = '';
        return $conf_database;
    }

    /**
     * rev
     * @param stdClass $data_cnx
     * @return PDO|array
     */
    private function link_db(stdClass $data_cnx): PDO|array
    {
        $conf_database = $this->conf_database(data_cnx: $data_cnx);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener conf db',data:  $conf_database);
        }

        $motor = $data_cnx->imp_database_tipo;
        $link = (new conexion())->genera_link_custom(conf_database: $conf_database,motor:  $motor);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al conectar con origen',data:  $link);
        }
        return $link;
    }

    /**
     * rev
     * @param int $imp_database_id
     * @param PDO $link
     * @return PDO|array
     */
    final public function link_destino(int $imp_database_id, PDO $link): PDO|array
    {
        $imp_database = (new imp_database(link: $link))->registro(registro_id: $imp_database_id,retorno_obj: true);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener imp_origen',data:  $imp_database);
        }

        $link_destino = $this->link_db(data_cnx: $imp_database);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al conectar con destino',data:  $link_destino);
        }
        return $link_destino;
    }

    /**
     * rev
     * @param int $imp_origen_id
     * @param PDO $link
     * @return PDO|array
     */
    public function link_origen(int $imp_origen_id, PDO $link): PDO|array
    {
        $imp_origen = (new imp_origen(link: $link))->registro(registro_id: $imp_origen_id,retorno_obj: true);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener imp_origen',data:  $imp_origen);
        }
        $link_origen = $this->link_db(data_cnx: $imp_origen);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al conectar con origen',data:  $link_origen);
        }
        return $link_origen;
    }
}
