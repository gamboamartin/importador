<?php
namespace gamboamartin\importador\models;
use base\orm\_modelo_parent;
use PDO;

class adm_tipo_dato extends \gamboamartin\administrador\models\adm_tipo_dato {

    public function __construct(PDO $link, array $childrens = array(), array $columnas_extra = array())
    {
        $columnas_extra['adm_tipo_dato_n_campos'] = /** @lang sql */
            "(SELECT COUNT(*) FROM adm_campo WHERE adm_campo.adm_tipo_dato_id = adm_tipo_dato.id)";
        parent::__construct(link: $link,childrens:  $childrens,columnas_extra:  $columnas_extra);
    }


}