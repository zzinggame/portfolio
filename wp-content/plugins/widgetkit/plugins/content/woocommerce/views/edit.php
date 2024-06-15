<?php

    global $wpdb;

    $fields =  $wpdb->get_col($wpdb->prepare("SELECT DISTINCT meta_key FROM {$wpdb->postmeta} WHERE meta_key NOT LIKE %s ORDER BY meta_key", $wpdb->esc_like( '_' ).'%'));

?>

<div class="uk-form-horizontal" ng-class="vm.name == 'contentCtrl' ? 'uk-width-2-3@l uk-width-1-2@xl' : ''" ng-controller="woocommerceCtrl as wc">

    <h3 class="uk-heading-divider">{{'Content' | trans}}</h3>

    <div class="uk-margin">
        <label class="uk-form-label" for="wk-category">{{'Category' | trans}}</label>
        <div class="uk-form-controls">
            <select id="wk-category" class="uk-select uk-form-width-large" ng-model="content.data['category']">
                <option value="">{{'All' | trans}}</option>
                <?php foreach (get_categories(array('taxonomy' => 'product_cat')) as $category) : ?>
                    <option value="<?= $category->cat_ID ?>"><?= $category->name ?></option>
                <?php endforeach ?>
            </select>
        </div>
    </div>

    <div class="uk-margin">
        <label class="uk-form-label" for="wk-number">{{'Limit' | trans}}</label>
        <div class="uk-form-controls">
            <input id="wk-number" class="uk-input uk-form-width-large" type="number" value="5" min="1" step="1" ng-model="content.data['number']">
        </div>
    </div>

    <div class="uk-margin">
        <label class="uk-form-label" for="wk-order">{{'Order' | trans}}</label>
        <div class="uk-form-controls">
            <select id="wk-order" class="uk-select uk-form-width-large" ng-model="content.data['order_by']">
                <option value="post_none">{{'Default' | trans}}</option>
                <option value="post_date">{{'Latest First' | trans}}</option>
                <option value="post_date_asc">{{'Latest Last' | trans}}</option>
                <option value="post_title">{{'Alphabetical' | trans}}</option>
                <option value="post_title_asc">{{'Alphabetical Reversed' | trans}}</option>
                <option value="post_price">{{'Price' | trans}}</option>
                <option value="post_price_asc">{{'Price Reversed' | trans}}</option>
                <option value="post_sales">{{'Sales' | trans}}</option>
                <option value="post_sales_asc">{{'Sales Reversed' | trans}}</option>
                <option value="rand">{{'Random' | trans}}</option>
            </select>
        </div>
    </div>

    <h3 class="uk-heading-divider uk-margin-large-top">{{'Mapping' | trans}}</h3>

    <div class="uk-margin">
        <span class="uk-form-label">{{'Fields' | trans}}</span>
        <div class="uk-form-controls">

            <div class="uk-grid uk-grid-small uk-child-width-1-2">
                <div>
                    <input class="uk-input" type="text" value="content" disabled>
                </div>
                <div>
                    <select class="uk-select" ng-model="content.data['content']">
                        <option value="intro">{{'Intro Text' | trans}}</option>
                        <option value="full">{{'Full Text' | trans}}</option>
                        <option value="excerpt">{{'Excerpt' | trans}}</option>
                    </select>
                </div>
            </div>

            <div class="uk-grid uk-grid-small uk-child-width-1-2">
                <div>
                    <input class="uk-input" type="text" value="date" disabled>
                </div>
                <div>
                    <select class="uk-select" ng-model="content.data['date']">
                        <option value="">{{'None' | trans}}</option>
                        <option value="publish_up">{{'Publish Up' | trans}}</option>
                    </select>
                </div>
            </div>

            <div class="uk-grid uk-grid-small uk-child-width-1-2">
                <div>
                    <input class="uk-input" type="text" value="author" disabled>
                </div>
                <div>
                    <select class="uk-select" ng-model="content.data['author']">
                        <option value="">{{'None' | trans}}</option>
                        <option value="author">{{'Author' | trans}}</option>
                    </select>
                </div>
            </div>

            <div class="uk-grid uk-grid-small uk-child-width-1-2">
                <div>
                    <input class="uk-input" type="text" value="categories" disabled>
                </div>
                <div>
                    <select class="uk-select" ng-model="content.data['categories']">
                        <option value="">{{'None' | trans}}</option>
                        <option value="categories">{{'Categories' | trans}}</option>
                    </select>
                </div>
            </div>

            <div class="uk-grid uk-grid-small uk-child-width-1-2" ng-repeat="map in wc.mapping">
                <div>
                    <input class="uk-input" type="text" ng-model="map.name" placeholder="{{'Field name' | trans}}">
                </div>
                <div class="uk-flex uk-flex-middle">
                    <select class="uk-select" ng-model="map.field">
                        <?php foreach ($fields as $field) : ?>
                        <option value="<?= $field ?>"><?= $field ?></option>
                        <?php endforeach ?>
                    </select>
                    <a class="uk-margin-left uk-text-danger" ng-click="wc.deleteMap(map)"><span uk-icon="trash"></span></a>
                </div>
            </div>

            <div class="uk-margin">
                <a class="uk-button uk-button-default" ng-click="wc.addMap()">{{'Add' | trans}}</a>
            </div>

        </div>
    </div>

</div>
