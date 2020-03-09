<?php
/**
 * Created by PhpStorm.
 * User: developers
 * Date: 10/5/2018
 * Time: 2:12 PM
 */
use application\util\AppUtil;
use application\util\FilterUtils;
use application\util\i18next;
use application\util\SystemConstant;

$validateErrors = (isset($_V_DATA_TO_VIEW[SystemConstant::APP_VALIDATE_ERR_ATT])) ? $_V_DATA_TO_VIEW[SystemConstant::APP_VALIDATE_ERR_ATT] : array();
$appTable = (isset($_V_DATA_TO_VIEW['appTable'])) ? APP_VALIDATE_ERR_ATT['appTable'] : array();
$msgJsonGen = (isset($_V_DATA_TO_VIEW['msgJsonGen'])) ? $_V_DATA_TO_VIEW['msgJsonGen'] : "";
$routListGen = (isset($_V_DATA_TO_VIEW['routListGen'])) ? $_V_DATA_TO_VIEW['routListGen'] : "";
?>
<!-- Page-header start -->
<div class="page-header card">
    <div class="row align-items-end">
        <div class="col-lg-8">
            <div class="page-header-title">
                <i class="icofont icofont-file-code bg-c-blue"></i>
                <div class="d-inline">
                    <h4><?= i18next::getTranslation('model.app_table.app_table') ?></h4>
                    <span><?= i18next::getTranslation('base.form') ?></span>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="page-header-breadcrumb">
                <ul class="breadcrumb-title">
                    <li class="breadcrumb-item">
                        <a href="<?=_BASEURL.'dashboard'?>"><i class="icofont icofont-home"></i></a>
                    </li>
                    <li class="breadcrumb-item"><a href="<?=_BASEURL.'apptablelist'?>"><?= i18next::getTranslation('model.app_table.app_table') ?></a></li>
                    <li class="breadcrumb-item"><a href="javascript:void(0)"><?= i18next::getTranslation('base.arr_form_page', array('page' => i18next::getTranslation('model.app_table.app_table'))) ?></a></li>
                </ul>
            </div>
        </div>
    </div>
</div>
<!-- Page-header end -->

<div class="page-body">
    <div class="row">
        <div class="col-sm-12">

            <div class="card">
                <div class="card-header">
                    <div class="card-header-right">
                        <ul class="list-unstyled card-option" style="width: 35px;">
                            <li class=""><i class="icofont icofont-simple-left"></i></li>
                            <li><i class="icofont icofont-maximize full-card"></i></li>
                            <li><i class="icofont icofont-minus minimize-card"></i></li>
                            <li><i class="icofont icofont-refresh reload-card"></i></li>
                            <li><i class="icofont icofont-error close-card"></i></li>
                        </ul>
                    </div>
                </div>
                <div class="card-block">

                    <div id="createdSuccessBlock" class="m-b-10">
                        <?php
                        if(!AppUtil::isEmpty($msgJsonGen)){
                            ?>
                            <p>
                                <span>Copy this code inside "model": {} at /resources/lang/[all your lang folder]/translation.json and delete last ',' comma sign </span><br>
                                <code>
                                   <?=$msgJsonGen?>
                                </code>
                            </p>
                        <?php

                        }
                        ?>
                        <?php
                        if(!AppUtil::isEmpty($routListGen)){
                        ?>
                        <p>
                            <span>Copy this code to bottom line of /application/core/initRoutes.php</span><br>
                            <code>
                                <?=$routListGen?>
                            </code>
                        </p>
                            <?php

                        }
                        ?>
                        <a href="<?=_BASEURL.'apptablelist'?>" class="btn btn-danger m-b-0"><i class="ti-back-right"></i> <?= i18next::getTranslation('base.back') ?></a>
                    </div>


                    <h4 class="sub-title"><?=i18next::getTranslation('helper.text_require')?></h4>
                    <form role="form" id="form_search" action="<?=FilterUtils::filterServerUrl('REQUEST_URI')?>" enctype="multipart/form-data" method="post">

                        <div class="form-group <?=(array_key_exists('app_table_name', $validateErrors)) ? "has-danger" : ""?> row">
                            <label class="col-sm-2 col-form-label" for="app_table_name"><?=i18next::getTranslation('model.app_table.app_table_name')?><span class='text-danger'>*</span></label>
                            <div class="col-sm-10">
                                <input maxlength="80" type="text" value="<?=$appTable->getAppTableName()?>" name="app_table_name" id="app_table_name" class="form-control form-control-<?=(array_key_exists('app_table_name', $validateErrors)) ? "danger" : "normal"?>" >
                                <?=(array_key_exists('app_table_name', $validateErrors)) ? " <div class=\"col-form-label\">".$validateErrors['app_table_name']."</div>" : ""?>
                            </div>
                        </div>

                        <div class="form-group <?=(array_key_exists('description', $validateErrors)) ? "has-danger" : ""?> row">
                            <label class="col-sm-2 col-form-label" for="description"><?=i18next::getTranslation('model.app_table.description')?><span class='text-danger'>*</span></label>
                            <div class="col-sm-10">
                                <textarea name="description" id="description"
                                          class="form-control  max-textarea form-control-<?=(array_key_exists('description', $validateErrors)) ? "danger" : "normal"?>"
                                          maxlength="120" rows="4"><?=$appTable->getDescription()?></textarea>
                                <?=(array_key_exists('description', $validateErrors)) ? " <div class=\"col-form-label\">".$validateErrors['description']."</div>" : ""?>
                            </div>
                        </div>



                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">File You Want Create</label>
                            <div class="col-sm-10">
                                <div class="checkbox-fade fade-in-primary">
                                    <label>
                                        <input value="1" checked="checked" type="checkbox" name="model" id="model">
                                        <span class="cr"><i class="cr-icon icofont icofont-ui-check txt-primary"></i></span>
                                        <span>Model</span>
                                    </label>
                                </div>

                                <div class="checkbox-fade fade-in-primary">
                                    <label>
                                        <input value="1" checked="checked" type="checkbox" name="service" id="service">
                                        <span class="cr"><i class="cr-icon icofont icofont-ui-check txt-primary"></i></span>
                                        <span>Service</span>
                                    </label>
                                </div>

                                <div class="checkbox-fade fade-in-primary">
                                    <label>
                                        <input value="1" type="checkbox" name="controller" id="controller">
                                        <span class="cr"><i class="cr-icon icofont icofont-ui-check txt-primary"></i></span>
                                        <span>Controller</span>
                                    </label>
                                </div>

                                <div class="checkbox-fade fade-in-primary">
                                    <label>
                                        <input value="1" type="checkbox" name="validator" id="validator">
                                        <span class="cr"><i class="cr-icon icofont icofont-ui-check txt-primary"></i></span>
                                        <span>Validator</span>
                                    </label>
                                </div>

                                <div class="checkbox-fade fade-in-primary">
                                    <label>
                                        <input value="1" type="checkbox" name="vlist" id="vlist">
                                        <span class="cr"><i class="cr-icon icofont icofont-ui-check txt-primary"></i></span>
                                        <span>List View</span>
                                    </label>
                                </div>


                                <div class="checkbox-fade fade-in-primary">
                                    <label>
                                        <input value="1" type="checkbox" name="vview" id="vview">
                                        <span class="cr"><i class="cr-icon icofont icofont-ui-check txt-primary"></i></span>
                                        <span>Form View</span>
                                    </label>
                                </div>

                                <div class="checkbox-fade fade-in-primary">
                                    <label>
                                        <input value="1" type="checkbox" name="vmsg" id="vmsg">
                                        <span class="cr"><i class="cr-icon icofont icofont-ui-check txt-primary"></i></span>
                                        <span>Model Message</span>
                                    </label>
                                </div>

                                <div class="checkbox-fade fade-in-primary">
                                    <label>
                                        <input value="1" type="checkbox" name="vroute" id="vroute">
                                        <span class="cr"><i class="cr-icon icofont icofont-ui-check txt-primary"></i></span>
                                        <span>Route List</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Theme</label>
                            <div class="col-sm-10">
                                <select name="vtheme" id="vtheme" class="js-example-basic-single col-sm-12">
                                    <?php
                                    $themeList = SystemConstant::$THEME_LIST;
                                    if(!empty($themeList)){
                                        foreach ($themeList AS $theme){
                                            echo "<option value=\"$theme\">$theme</option>";
                                        }
                                    }
                                    ?>

                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-12">
                                <button type="submit" class="btn btn-success m-b-0"><i class="ti-save"></i> <?= i18next::getTranslation('base.save') ?></button>
                                <a href="<?=_BASEURL.'apptablelist'?>" class="btn m-b-0"><i class="ti-back-right"></i> <?= i18next::getTranslation('base.cancel') ?></a>
                            </div>
                        </div>

                    </form>


                </div>
            </div>


        </div>
    </div>
</div>