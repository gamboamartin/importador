<?php /** @var gamboamartin\acl\controllers\controlador_adm_menu $controlador  controlador en ejecucion */ ?>
<?php use config\views; ?>
<main class="main section-color-primary">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="widget  widget-box box-container form-main widget-form-cart" id="form">
                        <?php include (new views())->ruta_templates."head/title.php"; ?>
                        <?php include (new views())->ruta_templates."head/subtitulo.php"; ?>
                        <?php include (new views())->ruta_templates."mensajes.php"; ?>
                </div>
            </div>
        </div>
    </div>
</main>

<main class="main section-color-primary">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="widget widget-box box-container widget-mylistings table-responsive">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                        <th>Id</th>
                        <th>com_cliente_id</th>
                        <th>dp_pais_id</th>
                        <th>dp_estado_id</th>
                        <th>dp_municipio_id</th>
                        <th>dp_cp_id</th>
                        <th>dp_colonia_id</th>
                        <th>dp_calle_id</th>
                        <th>dp_colonia_postal_id</th>
                        <th>dp_calle_pertenece_id</th>
                        <th>dp_pais</th>
                        <th>dp_estado</th>
                        <th>dp_municipio</th>
                        <th>dp_cp</th>
                        <th>dp_colonia</th>
                        <th>dp_calle</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        foreach ($controlador->com_tmp_cte_dps as $com_tmp_cte_dp){
                        ?>
                            <tr>
                                <td><?php  echo $com_tmp_cte_dp['com_tmp_cte_dp_id'] ?></td>
                                <td><?php  echo $com_tmp_cte_dp['com_tmp_cte_dp_com_cliente_id'] ?></td>
                                <td><?php  echo $com_tmp_cte_dp['com_tmp_cte_dp_dp_pais_id'] ?></td>
                                <td><?php  echo $com_tmp_cte_dp['com_tmp_cte_dp_dp_estado_id'] ?></td>
                                <td><?php  echo $com_tmp_cte_dp['com_tmp_cte_dp_dp_municipio_id'] ?></td>
                                <td><?php  echo $com_tmp_cte_dp['com_tmp_cte_dp_dp_cp_id'] ?></td>
                                <td><?php  echo $com_tmp_cte_dp['com_tmp_cte_dp_dp_colonia_id'] ?></td>
                                <td><?php  echo $com_tmp_cte_dp['com_tmp_cte_dp_dp_calle_id'] ?></td>
                                <td><?php  echo $com_tmp_cte_dp['com_tmp_cte_dp_dp_colonia_postal_id'] ?></td>
                                <td><?php  echo $com_tmp_cte_dp['com_tmp_cte_dp_dp_calle_pertenece_id'] ?></td>
                                <td><?php  echo $com_tmp_cte_dp['com_tmp_cte_dp_dp_pais'] ?></td>
                                <td><?php  echo $com_tmp_cte_dp['com_tmp_cte_dp_dp_estado'] ?></td>
                                <td><?php  echo $com_tmp_cte_dp['com_tmp_cte_dp_dp_municipio'] ?></td>
                                <td><?php  echo $com_tmp_cte_dp['com_tmp_cte_dp_dp_cp'] ?></td>
                                <td><?php  echo $com_tmp_cte_dp['com_tmp_cte_dp_dp_colonia'] ?></td>
                                <td><?php  echo $com_tmp_cte_dp['com_tmp_cte_dp_dp_calle'] ?></td>
                            </tr>
                        <?php
                        }
                        ?>
                        </tbody>
                    </table>
                </div> <!-- /. widget-table-->
            </div><!-- /.center-content -->
        </div>
    </div>
</main>

