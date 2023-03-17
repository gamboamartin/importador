<?php
namespace gamboamartin\importador\models;
use base\orm\_modelo_parent;
use gamboamartin\administrador\models\adm_accion;
use gamboamartin\errores\errores;
use PDO;
use stdClass;

class imp_ultimo extends _modelo_parent{

    public function __construct(PDO $link){
        $tabla = 'imp_ultimo';
        $columnas = array($tabla=>false,'imp_destino'=>$tabla,'imp_origen'=>'imp_destino','imp_database'=>'imp_origen',
            'adm_seccion'=>'imp_origen','adm_namespace'=>'adm_seccion','adm_accion'=>$tabla);
        $campos_obligatorios[] = 'imp_destino_id';
        $campos_obligatorios[] = 'id_ultimo';
        $campos_obligatorios[] = 'adm_accion_id';



        $columnas_extra = array();

        $tipo_campos = array();


        parent::__construct(link: $link, tabla: $tabla, campos_obligatorios: $campos_obligatorios,
            columnas: $columnas, columnas_extra: $columnas_extra, tipo_campos: $tipo_campos);

        $this->NAMESPACE = __NAMESPACE__;
    }

    private function actualiza_id_ultimo(string $adm_accion_descripcion, int $id_ultimo, int $imp_destino_id){
        $imp_ultimo_id = $this->imp_ultimo_id_by_destino(adm_accion_descripcion: $adm_accion_descripcion, imp_destino_id: $imp_destino_id);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener ultimo',data:  $imp_ultimo_id);
        }
        $result = $this->modifica_id_ultimo(id_ultimo: $id_ultimo, imp_ultimo_id: $imp_ultimo_id);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al modificar ultimo',data:  $result);
        }
        return $result;
    }
    final public function alta_bd(array $keys_integra_ds = array('codigo', 'descripcion')): array|stdClass
    {
        if(!isset($this->registro['descripcion'])){
            $registro = $this->descripcion(id: -1, registro: $this->registro);
            if(errores::$error){
                return $this->error->error(mensaje: 'Error asignar descripcion', data: $registro);
            }
            $this->registro = $registro;
        }

        $r_alta_bd = parent::alta_bd(keys_integra_ds: $keys_integra_ds); // TODO: Change the autogenerated stub
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al dar de alta', data: $r_alta_bd);
        }
        return $r_alta_bd;
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
        $imp_destino_id = -1;

        if(!isset($registro['imp_destino_id'])){
            $existen_foraneas = false;
        }

        if(isset($registro['imp_destino_id'])){
            $imp_destino_id = $registro['imp_destino_id'];
        }

        if(!$existen_foraneas) {

            $registro_previo = $this->registro(registro_id: $id);
            if (errores::$error) {
                return $this->error->error(mensaje: 'Error al obtener registro_previo', data: $registro_previo);
            }

            if (!isset($registro['imp_destino_id'])) {
                $imp_destino_id = $registro_previo['imp_destino_id'];
            }

        }

        $imp_destino = (new imp_destino(link: $this->link))->registro(registro_id: $imp_destino_id);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener imp_destino', data: $imp_destino);
        }

        $descripcion = $imp_destino['imp_server_ip'].' '.$imp_destino['imp_database_descripcion'];
        $descripcion .= ' '.$imp_destino['adm_seccion_descripcion'];
        $registro['descripcion'] = $descripcion;
        return $registro;
    }

    public function ejecuta_ultimo(string $adm_accion_descripcion, int $id_ultimo, int $imp_destino_id){
        $existe = $this->existe_by_destino(adm_accion: $adm_accion_descripcion, imp_destino_id: $imp_destino_id);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al verificar si existe',data:  $existe);
        }

        if(!$existe){
            $result = $this->inserta_ultimo(adm_accion_descripcion: $adm_accion_descripcion, imp_destino_id: $imp_destino_id, id_ultimo: $id_ultimo);
            if(errores::$error){
                return $this->error->error(mensaje: 'Error al insertar ultimo',data:  $result);
            }
        }
        else{
            $result = $this->actualiza_id_ultimo(adm_accion_descripcion: $adm_accion_descripcion, id_ultimo: $id_ultimo, imp_destino_id: $imp_destino_id);
            if(errores::$error){
                return $this->error->error(mensaje: 'Error al modificar ultimo',data:  $result);
            }
        }
        return $result;
    }

    public function existe_by_destino(string $adm_accion, int $imp_destino_id){
        $filtro['imp_destino.id'] =  $imp_destino_id;
        $filtro['adm_accion.descripcion'] =  $adm_accion;
        $existe = $this->existe(filtro: $filtro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al verificar si existe',data:  $existe);
        }
        return $existe;
    }

    /**
     * Obtiene el registro de imp_ultimo basado en la descripcion de la accion y el destino
     * @param string $adm_accion_descripcion Accion en ejecucion
     * @param int $imp_destino_id Registro de destino
     * @return array
     * @version 0.44.0
     *
     */
    final public function imp_ultimo_by_destino(string $adm_accion_descripcion, int $imp_destino_id): array
    {

        $adm_accion_descripcion = trim($adm_accion_descripcion);
        if($adm_accion_descripcion === ''){
            return $this->error->error(mensaje: 'Error adm_accion_descripcion esta vacio',
                data:  $adm_accion_descripcion);
        }
        if($imp_destino_id<=0){
            return $this->error->error(mensaje: 'Error imp_destino_id debe ser mayor a 0',data:  $imp_destino_id);
        }

        $filtro['imp_destino.id'] =  $imp_destino_id;
        $filtro['adm_accion.descripcion'] =  $adm_accion_descripcion;

        $r_imp_ultimo = $this->filtro_and(filtro: $filtro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener ultimo',data:  $r_imp_ultimo);
        }
        if((int)$r_imp_ultimo->n_registros === 0){
            return $this->error->error(mensaje: 'Error al no existe ultimo',data:  $r_imp_ultimo);
        }
        if((int)$r_imp_ultimo->n_registros > 1){
            return $this->error->error(mensaje: 'Error de integridad existe mas de un row',data:  $r_imp_ultimo);
        }

        return $r_imp_ultimo->registros[0];
    }
    private function imp_ultimo_id_by_destino(string $adm_accion_descripcion, int $imp_destino_id){
        $imp_ultimo = $this->imp_ultimo_by_destino(adm_accion_descripcion: $adm_accion_descripcion, imp_destino_id: $imp_destino_id);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener ultimo',data:  $imp_ultimo);
        }

        return $imp_ultimo['imp_ultimo_id'];
    }


    final public function imp_destino_id(int $imp_ultimo_id){
        $imp_ultimo = $this->registro(registro_id: $imp_ultimo_id);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener ultimo',data:  $imp_ultimo);
        }

        return $imp_ultimo['imp_destino_id'];
    }

    public function inserta_ultimo(string $adm_accion_descripcion, int $imp_destino_id, int $id_ultimo){

        $adm_accion = (new adm_accion(link: $this->link))->accion_registro(accion: $adm_accion_descripcion,seccion:  'imp_destino');
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener accion',data:  $adm_accion);
        }

        $imp_ultimo['imp_destino_id'] = $imp_destino_id;
        $imp_ultimo['id_ultimo'] = $id_ultimo;
        $imp_ultimo['adm_accion_id'] = $adm_accion['adm_accion_id'];
        $result = $this->alta_registro(registro: $imp_ultimo);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al insertar ultimo',data:  $result);
        }
        return $result;
    }

    final public function modifica_bd(array $registro, int $id, bool $reactiva = false, array $keys_integra_ds = array('codigo', 'descripcion')): array|stdClass
    {

        if(!isset($registro['descripcion'])){
            $registro = $this->descripcion(id: $id, registro: $registro);
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

    private function modifica_id_ultimo(int $id_ultimo, int $imp_ultimo_id){

        $imp_ultimo_upd['id_ultimo'] = $id_ultimo;
        $r_upd_bd = $this->modifica_bd(registro: $imp_ultimo_upd, id: $imp_ultimo_id);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al insertar ultimo',data:  $r_upd_bd);
        }
        return $r_upd_bd;
    }


}