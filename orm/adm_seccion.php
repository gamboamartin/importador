<?php
namespace gamboamartin\importador\models;
use base\orm\_modelo_parent;
use PDO;

class adm_seccion extends \gamboamartin\administrador\models\adm_seccion {

    public function __construct(PDO $link, array $childrens = array(), array $columnas_extra = array())
    {
        $columnas_extra['adm_seccion_n_origenes'] = /** @lang sql */
            "(SELECT COUNT(*) FROM imp_origen WHERE imp_origen.adm_seccion_id = adm_seccion.id)";

        $columnas_extra['adm_seccion_n_campos'] = /** @lang sql */
            "(SELECT COUNT(*) FROM adm_campo WHERE adm_campo.adm_seccion_id = adm_seccion.id)";
        parent::__construct(link: $link,childrens:  $childrens,columnas_extra:  $columnas_extra);
    }


}