<?php
namespace gamboamartin\importador\html;

use gamboamartin\administrador\models\adm_tipo_dato;
use gamboamartin\errores\errores;
use gamboamartin\importador\models\imp_destino;
use gamboamartin\system\html_controler;
use PDO;


class adm_tipo_dato_html extends html_controler {

    public function select_adm_tipo_dato_id(int $cols, bool $con_registros, int $id_selected, PDO $link, bool $disabled = false): array|string
    {
        $modelo = new adm_tipo_dato(link: $link);

        $select = $this->select_catalogo(cols: $cols, con_registros: $con_registros, id_selected: $id_selected,
            modelo: $modelo, disabled: $disabled, label: 'Tipo Dato', required: true);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al generar select', data: $select);
        }
        return $select;
    }


}
