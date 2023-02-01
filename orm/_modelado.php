<?php
namespace gamboamartin\importador\models;
use base\orm\modelo;
use gamboamartin\errores\errores;
use PDO;
use stdClass;

class _modelado{
    private errores $error;
    public function __construct(){
        $this->error = new errores();
    }





    /**
     * rev
     * @param int $imp_database_id
     * @param PDO $link
     * @param string $name_model
     * @return array|modelo
     */
    final public function destino(int $imp_database_id, PDO $link, string $name_model):array|modelo{
        $link_destino = (new _conexion())->link_destino(imp_database_id: $imp_database_id, link: $link);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al conectar con destino',data:  $link_destino);
        }
        /**
         * @var modelo $name_model;
         */
        return new $name_model(link: $link_destino);
    }







    /**
     * rev
     * @param int $imp_destino_ultimo_id_origen
     * @param string $tabla
     * @return array
     */
    private function filtro_extra(int $imp_destino_ultimo_id_origen, string $tabla): array
    {
        $filtro_extra[0][$tabla.'.id']['operador'] = '>';
        $filtro_extra[0][$tabla.'.id']['valor'] = $imp_destino_ultimo_id_origen;
        $filtro_extra[0][$tabla.'.id']['comparacion'] = 'AND';
        return $filtro_extra;

    }

    /**
     * rev
     * @param array $row
     * @return array
     */
    public function limpia_data_row(array $row): array
    {
        unset($row['usuario_update_id']);
        unset($row['usuario_alta_id']);
        return $row;
    }



    /**
     * rev
     * @param int $imp_origen_id
     * @param PDO $link
     * @param string $name_model
     * @return modelo|array
     */
    private function origen(int $imp_origen_id,  PDO $link, string $name_model): modelo|array
    {
        $link_origen = (new _conexion())->link_origen(imp_origen_id: $imp_origen_id, link: $link);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al conectar con origen',data:  $link_origen);
        }
        /**
         * @var modelo $name_model;
         */
        return new $name_model(link: $link_origen);
    }

    /**
     * rev
     * @param int $imp_destino_ultimo_id_origen
     * @param int $imp_origen_id
     * @param int $limit
     * @param PDO $link
     * @param string $name_model
     * @return array
     */
    final public function rows_origen(int $imp_destino_ultimo_id_origen, int $imp_origen_id, int $limit, PDO $link, string $name_model): array
    {
        $origen = $this->origen(imp_origen_id: $imp_origen_id, link: $link, name_model: $name_model);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al conectar con origen',data:  $origen);
        }

        $filtro_extra = $this->filtro_extra(
            imp_destino_ultimo_id_origen: $imp_destino_ultimo_id_origen, tabla: $origen->tabla);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener filtro',data:  $filtro_extra);
        }

        $r_rows = $origen->filtro_and(columnas_en_bruto: true, con_sq: false, filtro_extra: $filtro_extra, limit: $limit);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener registros',data:  $r_rows);
        }
        return $r_rows->registros;

    }


}
