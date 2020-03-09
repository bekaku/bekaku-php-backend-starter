<?php
use application\model\AppPermission;
use application\util\ControllerUtil;
use application\util\FilterUtils;
use application\util\i18next;
use application\util\SystemConstant;

$appTableList = (isset($_V_DATA_TO_VIEW['appTableList'])) ? $_V_DATA_TO_VIEW['appTableList'] : array();
$appPagination = (isset($_V_DATA_TO_VIEW[SystemConstant::APP_PAGINATION_ATT])) ? $_V_DATA_TO_VIEW[SystemConstant::APP_PAGINATION_ATT] : '';
?>

<!-- Page-header start -->
<div class="page-header card">
    <div class="row align-items-end">
        <div class="col-lg-8">
            <div class="page-header-title">
                <i class="icofont icofont-list bg-c-blue"></i>
                <div class="d-inline">
                    <h4><?= i18next::getTranslation('model.app_table.app_table') ?></h4>
                    <span><?= i18next::getTranslation('base.list') ?></span>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="page-header-breadcrumb">
                <ul class="breadcrumb-title">
                    <li class="breadcrumb-item">
                        <a href="<?=_BASEURL.'dashboard'?>">
                            <i class="icofont icofont-home"></i>
                        </a>
                    </li>
                    <li class="breadcrumb-item"><a href="javascript:void(0)"><?= i18next::getTranslation('model.app_table.app_table') ?></a></li>
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
                    <div class="btn-group " role="group">
                        <a href="<?=_BASEURL.'apptableadd'?>" class="btn btn-outline-primary"><i class="icofont icofont-ui-add"></i> <?= i18next::getTranslation('base.add_new') ?></a>
                        <button type="button" class="btn btn-danger btn-outline-danger app-delete-seleted-confirm"
                                app-parameter="<?=ControllerUtil::encodeParamId(AppPermission::$tableName)?>" app-delete-all-url="<?=_BASEURL.''?>">
                            <i class="icofont icofont-delete-alt"></i> <?= i18next::getTranslation('base.delete_seleted') ?>
                        </button>
                    </div>
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

                    <!-- Start Search-->
                    <div class="row">
                        <div class="col-md-12 v-bg-main-6">

                            <form role="form" id="form_search" method="get">
                                <div class="form-group row">

                                    <div class="col-md-4">
                                        <label class="col-form-label">&nbsp;</label>
                                        <input id="q_app_table_name" name="q_app_table_name" value="<?=FilterUtils::filterGetString('q_app_table_name')?>" type="text" class="form-control form-control-normal"
                                               placeholder="<?= i18next::getTranslation('model.app_table.app_table_name') ?>">
                                    </div>

                                    <div class="col-md-4">
                                        <label class="col-form-label">&nbsp;</label>
                                        <input id="q_description" name="q_description" value="<?=FilterUtils::filterGetString('q_description')?>" type="text" class="form-control form-control-normal"
                                               placeholder="<?= i18next::getTranslation('model.app_table.description') ?>">
                                    </div>

                                </div>

                                <div class="form-group row">
                                    <div class="col-sm-12">
                                        <button type="submit" class="btn btn-primary m-b-0"><i class="icofont icofont-search-alt-1"></i> <?= i18next::getTranslation('base.search') ?></button>
                                    </div>
                                </div>

                            </form>
                        </div>
                    </div>
                    <!-- End Search-->

                    <div class="table-responsive">
                        <div class="table-content">
                            <table class="table table-striped dt-responsive nowrap">
                                <thead>
                                <tr>
                                    <th style="width:1%">
                                        <div class="checkbox-fade fade-in-primary" data-toggle="tooltip" data-placement="top"
                                             title="" data-original-title="<?= i18next::getTranslation('base.select_all') ?>">
                                            <label>
                                                <input type="checkbox" name="checkBoxAll" id="checkBoxAll">
                                                <span class="cr">
                                                      <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
                                                    </span>
                                                <span>&nbsp;</span>
                                            </label>
                                        </div>
                                    </th>
                                    <th style="vertical-align: middle;">
                                        <a href="<?=ControllerUtil::uriSortConcat('app_table_name','ASC')?>">
                                            <?= i18next::getTranslation('model.app_table.app_table_name') ?>
                                        </a>
                                        <span id="displaySort_name"></i></span>
                                    </th>
                                    <th style="vertical-align: middle;">
                                        <a href="<?=ControllerUtil::uriSortConcat('description','ASC')?>">
                                            <?= i18next::getTranslation('model.app_table.description') ?>
                                        </a>
                                        <span id="displaySort_description"></i></span>
                                    </th>
                                    <th style="vertical-align: middle;"><?= i18next::getTranslation('base.tool') ?></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                if($appTableList) {
                                foreach ($appTableList AS $appTable) {
                                ?>
                                    <tr id="hide_tr_<?=$appTable->getId()?>">
                                        <td>
                                            <div class="checkbox-fade fade-in-primary">
                                                <label>
                                                    <input value="<?=$appTable->getId()?>" type="checkbox" name="check" id="checkbox1<?=$appTable->getId()?>">
                                                    <span class="cr">
                                                      <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
                                                    </span>
                                                    <span>&nbsp;</span>
                                                </label>
                                            </div>
                                        </td>
                                        <td><p><?=$appTable->getAppTableName()?></p></td>
                                        <td><p><?=$appTable->getDescription()?></p></td>
                                        <td class="action-icon">
                                            <a href="<?=_BASEURL.'apptableedit?'.ControllerUtil::genParamId($appTable)?>" class="m-r-15 text-muted" data-toggle="tooltip"
                                               data-placement="top" title="" data-original-title="<?=i18next::getTranslation('base.edit')?>">
                                                <i class="icofont icofont-ui-edit"></i>
                                            </a>
                                            <a href="javascript:void(0)" data-href="<?=_BASEURL.'apptabledelete?'.ControllerUtil::genParamId($appTable)?>"
                                               data-id-hide="hide_tr_<?=$appTable->getId()?>"
                                               class="app-delete-single-confirm"
                                               data-toggle="tooltip" data-placement="top" title="" data-original-title="<?=i18next::getTranslation('base.delete')?>">
                                                <i class="icofont icofont-delete-alt text-danger"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php
                                    }
                                }
                                ?>

                                </tbody>
                            </table>
                            <nav aria-label="...">
                                <?=$appPagination;?>
                            </nav>

                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
