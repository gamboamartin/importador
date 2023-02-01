<?php
namespace gamboamartin\importador\models;
use base\orm\_modelo_parent;
use gamboamartin\errores\errores;
use PDO;
use stdClass;

class imp_destino extends _modelo_parent{

    public function __construct(PDO $link){
        $tabla = 'imp_destino';
        $columnas = array($tabla=>false,'imp_origen'=>$tabla,'imp_database'=>$tabla,'imp_server'=>'imp_database',
            'adm_seccion'=>'imp_origen','adm_namespace'=>'adm_seccion');
        $campos_obligatorios[] = 'descripcion';
        $campos_obligatorios[] = 'descripcion_select';
        $campos_obligatorios[] = 'imp_origen_id';
        $campos_obligatorios[] = 'imp_database_id';
        $campos_obligatorios[] = 'ultimo_id_origen';
        $campos_obligatorios[] = 'fecha_ultima_ejecucion';


        $columnas_extra = array();

        $tipo_campos = array();


        parent::__construct(link: $link, tabla: $tabla, campos_obligatorios: $campos_obligatorios,
            columnas: $columnas, columnas_extra: $columnas_extra, tipo_campos: $tipo_campos);

        $this->NAMESPACE = __NAMESPACE__;
    }

    public function alta_bd(array $keys_integra_ds = array('codigo', 'descripcion')): array|stdClass
    {
        if(!isset($this->registro['descripcion'])){
            $registro = $this->descripcion(id: -1, registro: $this->registro);
            if(errores::$error){
                return $this->error->error(mensaje: 'Error asignar descripcion', data: $registro);
            }
            $this->registro = $registro;
        }
        if(!isset($this->registro['ultimo_id_origen'])){
            $this->registro['ultimo_id_origen'] = 0;
        }
        if(!isset($this->registro['fecha_ultima_ejecucion'])){
            $this->registro['fecha_ultima_ejecucion'] = date('Y-m-d H:i:s');
        }
        $r_alta_bd = parent::alta_bd(keys_integra_ds: $keys_integra_ds); // TODO: Change the autogenerated stub
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al dar de alta', data: $r_alta_bd);
        }
        return $r_alta_bd;
    }

    public function alta_full(int $imp_destino_id){

        $imp_destino = $this->registro(registro_id: $imp_destino_id);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener destino',data:  $imp_destino);
        }


        $ejecuciones = (new _inserciones())->aplica_inserciones(imp_destino: $imp_destino,link: $this->link, usuario_id: $this->usuario_id);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al insertar registros',data:  $ejecuciones);
        }

        $r_imp_destino = $this->upd_destino(imp_destino_id: $this->registro_id,ultimo_id_origen:  $ejecuciones->ultimo_id_origen);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al modificar destino',data:  $r_imp_destino);
        }

        $ejecuciones->r_imp_destino = $r_imp_destino;

        return $ejecuciones;
    }

    /**
     * Integra la descripcion en un registro en proceso de alta o modifica
     * @param int $id Identificador de la entidad
     * @param array $registro Registro en proceso
     * @return array
     */
    private function descripcion(int $id, array $registro): array
    {
        $existen_foraneas = true;
        $imp_database_id = -1;
        $imp_origen_id = -1;
        if(!isset($registro['imp_origen_id'])|| !isset($registro['imp_database_id'])){
            $existen_foraneas = false;
        }
        if(isset($registro['imp_origen_id'])){
            $imp_origen_id = $registro['imp_origen_id'];
        }
        if(isset($registro['imp_database_id'])){
            $imp_database_id = $registro['imp_database_id'];
        }

        if(!$existen_foraneas) {
            $registro_previo = $this->registro(registro_id: $id);
            if (errores::$error) {
                return $this->error->error(mensaje: 'Error al obtener registro_previo', data: $registro_previo);
            }

            if (!isset($registro['imp_origen_id'])) {
                $imp_origen_id = $registro_previo['imp_origen_id'];
            }
            if (!isset($registro['imp_database_id'])) {
                $imp_database_id = $registro_previo['imp_database_id'];
            }

        }

        $imp_origen = (new imp_origen(link: $this->link))->registro(registro_id: $imp_origen_id);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener imp_origen', data: $imp_origen);
        }
        $imp_database = (new imp_database(link: $this->link))->registro(registro_id: $imp_database_id);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener imp_database', data: $imp_database);
        }


        $descripcion = $imp_origen['imp_server_ip'].' '.$imp_origen['imp_database_descripcion'];
        $descripcion .= ' '.$imp_origen['adm_seccion_descripcion'];
        $descripcion .= ' '.$imp_database['imp_database_descripcion'].' '.$imp_database['imp_server_ip'];
        $descripcion .= ' '.$imp_database['imp_server_domain'];

        $registro['descripcion'] = $descripcion;
        return $registro;
    }

    public function inserta_ultimos(int $imp_destino_id){

        $imp_destino = $this->registro(registro_id: $imp_destino_id);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener destino',data:  $imp_destino);
        }


        $ejecuciones = (new _inserciones())->aplica_inserciones_ultimas(imp_destino: $imp_destino,link: $this->link,
            usuario_id: $this->usuario_id);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al insertar registros',data:  $ejecuciones);
        }

        $r_imp_destino = $this->upd_destino(imp_destino_id: $this->registro_id,
            ultimo_id_origen:  $ejecuciones->ultimo_id_origen);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al modificar destino',data:  $r_imp_destino);
        }

        $ejecuciones->r_imp_destino = $r_imp_destino;

        return $ejecuciones;
    }

    public function modifica_bd(array $registro, int $id, bool $reactiva = false, array $keys_integra_ds = array('codigo', 'descripcion')): array|stdClass
    {


        if(!isset($registro['descripcion'])){
            $registro = $this->descripcion(id: $this->registro_id, registro: $registro);
            if(errores::$error){
                return $this->error->error(mensaje: 'Error asignar descripcion', data: $registro);
            }
        }


        $r_modifica_bd= parent::modifica_bd(registro: $registro, id: $id,reactiva:  $reactiva,keys_integra_ds:  $keys_integra_ds); // TODO: Change the autogenerated stub
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al modificar', data: $r_modifica_bd);
        }
        return $r_modifica_bd;

    }

    public function modifica_full(int $imp_destino_id){

        $imp_destino = $this->registro(registro_id: $imp_destino_id);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener destino',data:  $imp_destino);
        }

        $ejecuciones = (new _modificaciones())->aplica_modificaciones(imp_destino: $imp_destino,link: $this->link, usuario_id: $this->usuario_id);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al modificar registros',data:  $ejecuciones);
        }

        $r_imp_destino = $this->upd_destino(imp_destino_id: $this->registro_id,ultimo_id_origen:  $ejecuciones->ultimo_id_origen);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al modificar destino',data:  $r_imp_destino);
        }

        $ejecuciones->r_imp_destino = $r_imp_destino;

        return $ejecuciones;
    }

    private function upd_destino(int $imp_destino_id, int $ultimo_id_origen): array|stdClass
    {
        $imp_destino_upd['ultimo_id_origen'] = $ultimo_id_origen;

        $r_imp_destino = $this->modifica_bd(registro:$imp_destino_upd,id:  $imp_destino_id);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al modificar destino',data:  $r_imp_destino);
        }
        return $r_imp_destino;
    }

}