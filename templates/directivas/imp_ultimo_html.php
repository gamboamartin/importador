<?php
namespace gamboamartin\importador\html;

use gamboamartin\errores\errores;
use gamboamartin\importador\models\imp_ultimo;
use gamboamartin\system\html_controler;
use PDO;

class imp_ultimo_html extends html_controler {

    public function select_imp_ultimo_id(int $cols, bool $con_registros, int $id_selected, PDO $link,
                                         bool $disabled = false): array|string
    {
        $modelo = new imp_ultimo(link: $link);

        $select = $this->select_catalogo(cols: $cols, con_registros: $con_registros, id_selected: $id_selected,
            modelo: $modelo, disabled: $disabled, label: 'Ultimo', required: true);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al generar select', data: $select);
        }
        return $select;
    }


}
