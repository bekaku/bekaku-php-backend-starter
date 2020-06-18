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

$validateErrors = (!empty($_V_DATA_TO_VIEW) && isset($_V_DATA_TO_VIEW['errors'])) ? $_V_DATA_TO_VIEW['errors'] : array();
$appTable = (!empty($_V_DATA_TO_VIEW) && isset($_V_DATA_TO_VIEW['appTable'])) ? $_V_DATA_TO_VIEW['appTable'] : array();
$msgJsonGen = (!empty($_V_DATA_TO_VIEW) && isset($_V_DATA_TO_VIEW['msgJsonGen'])) ? $_V_DATA_TO_VIEW['msgJsonGen'] : "";
$routListGen = (!empty($_V_DATA_TO_VIEW) && isset($_V_DATA_TO_VIEW['routListGen'])) ? $_V_DATA_TO_VIEW['routListGen'] : "";
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Hello Bekaku!</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.8.0/css/bulma.min.css">
    <script defer src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.12.1/js/all.min.js"></script>
</head>
<body>
<section class="section">
    <div class="container">
        <h1 class="title">
            Generate Starter Template
        </h1>
        <p class="subtitle">
            Generate Backend Starter Resource<strong></strong>!
        </p>

        <div class="columns">
            <div class="column">

                <?php
                if (!empty($msgJsonGen) || !empty($routListGen)) {
                    ?>
                    <div style="margin-bottom: 25px;">

                        <?php
                        if (!AppUtil::isEmpty($msgJsonGen)) {
                            ?>
                            <div>
                                <div style="margin-top: 25px;margin-bottom: 25px;">Copy this code inside "model": {} at
                                    /resources/lang/[all your lang folder]/translation.json and delete last ',' comma
                                    sign
                                </div>
                                <code>
                                    <?= $msgJsonGen ?>
                                </code>
                            </div>
                            <?php

                        }
                        ?>
                        <?php
                        if (!AppUtil::isEmpty($routListGen)) {
                            ?>
                            <div>
                                <div style="margin-top: 25px;margin-bottom: 25px;">Copy this code to bottom line of
                                    /application/core/initRoutes.php
                                </div>
                                <code>
                                    <?= $routListGen ?>
                                </code>
                            </div>
                            <?php

                        }
                        ?>
                    </div>
                <?php } ?>


                <form role="form" id="form_search" action="<?= FilterUtils::filterServerUrl('REQUEST_URI') ?>"
                      enctype="multipart/form-data" method="post">

                    <div class="field">
                        <label class="label">Table Name</label>
                        <div class="control has-icons-left has-icons-right">
                            <input maxlength="80" placeholder="Table Name" type="text"
                                   value="" name="app_table_name" id="app_table_name"
                                   class="input <?= (array_key_exists('app_table_name', $validateErrors)) ? "is-danger" : "" ?>">
                            <span class="icon is-small is-left"><i class="fas fa-table"></i></span>
                        </div>
                        <?= (array_key_exists('app_table_name', $validateErrors)) ? " <p class=\"help is-danger\">" . $validateErrors['app_table_name'] . "</p>" : "" ?>
                    </div>
                    <div class="field">
                        <label class="label">Description</label>
                        <textarea name="description" id="description"
                                  class="textarea <?= (array_key_exists('description', $validateErrors)) ? "is-danger" : "" ?>">description</textarea>
                        <?= (array_key_exists('description', $validateErrors)) ? " <p class=\"help is-danger\">" . $validateErrors['description'] . "</p>" : "" ?>
                    </div>

                    <div class="field">
                        <label class="label">File You Want Create</label>
                        <label class="checkbox">
                            <input value="1" checked="checked" type="checkbox" name="model" id="model">
                            Model
                        </label>
                        <label class="checkbox">
                            <input value="1" checked="checked" type="checkbox" name="service" id="service">
                            Service
                        </label>
                        <label class="checkbox">
                            <input value="1" checked type="checkbox" name="controller" id="controller">
                            Controller
                        </label>
                        <label class="checkbox">
                            <input value="1" checked type="checkbox" name="validator" id="validator">
                            Validator
                        </label>
                        <label class="checkbox">
                            <input value="1" checked type="checkbox" name="vlist" id="vlist">
                            List View
                        </label>
                        <label class="checkbox">
                            <input value="1" checked type="checkbox" name="vview" id="vview">
                            Form View
                        </label>
                        <label class="checkbox">
                            <input value="1" checked type="checkbox" name="vmsg" id="vmsg">
                            Model Message
                        </label>
                        <label class="checkbox">
                            <input value="1" checked type="checkbox" name="vroute" id="vroute">
                            Route List
                        </label>
                    </div>
                    <div class="field">
                        <label class="label">Model</label>
                        <label class="checkbox">
                            <input value="1" checked type="checkbox" name="auditInfo" id="auditInfo">
                            Audit info
                        </label>
                    </div>


                    <div class="field is-grouped">
                        <div class="control">
                            <button type="submit" class="button is-link"><i class="fas fa-plus"
                                                                            style="margin-right: 5px;"></i><?= i18next::getTranslation('base.save') ?>
                            </button>
                        </div>
                        <div class="control">
                            <button class="button is-link is-light">Cancel</button>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
</section>
</body>
</html>