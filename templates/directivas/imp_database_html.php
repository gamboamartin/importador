<?php
namespace gamboamartin\importador\html;

use gamboamartin\errores\errores;
use gamboamartin\importador\models\imp_database;
use gamboamartin\system\html_controler;
use PDO;


class imp_database_html extends html_controler {

    public function select_imp_server_id(int $cols, bool $con_registros, int $id_selected, PDO $link): array|string
    {
        $modelo = new imp_database(link: $link);

        $select = $this->select_catalogo(cols:$cols,con_registros:$con_registros,id_selected:$id_selected,
            modelo: $modelo,label: 'DB',required: true);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al generar select', data: $select);
        }
        return $select;
    }


}
