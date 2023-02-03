<?php
namespace gamboamartin\importador\tests;
use base\orm\modelo_base;
use gamboamartin\errores\errores;
use gamboamartin\importador\models\imp_ultimo;
use PDO;


class base_test{

    public function alta_imp_ultimo(PDO $link, int $adm_accion_id = 1, int $id = 1, $descripcion = '1', int $imp_destino_id = 1): array|\stdClass
    {

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
