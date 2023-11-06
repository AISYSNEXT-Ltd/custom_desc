<?php

/**
 * 2007-2023 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author    PrestaShop SA <contact@prestashop.com>
 *  @copyright 2007-2023 PrestaShop SA
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class Custom_desc extends Module
{
    protected $config_form = false;

    public function __construct()
    {
        $this->name = 'custom_desc';
        $this->tab = 'administration';
        $this->version = '1.0.0';
        $this->author = 'AISYSNEXT';
        $this->need_instance = 0;

        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('AISYSNEXT Custom Category');
        $this->description = $this->l('Add short description to category');

        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install()
    {


        return $this->addCategorylangTable() && parent::install() &&
            $this->registerHook('header') &&
            $this->registerHook('displayBackOfficeHeader') &&
            $this->registerHook('actionCategoryFormBuilderModifier') &&
            $this->registerHook('actionAfterCreateCategoryFormHandler') &&
            $this->registerHook('actionAfterUpdateCategoryFormHandler');
    }

    public function uninstall()
    {
        return $this->removeCategorylangTable() && parent::uninstall();
    }


    protected function addCategorylangTable()
    {
        Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'category_lang` ADD `shortdesc` TEXT NULL AFTER `description`');
        return true;
    }

    protected function removeCategorylangTable()
    {
        Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'category_lang` DROP COLUMN `shortdesc`');
        return true;
    }


    public function hookActionCategoryFormBuilderModifier(array $params)
    {

        $formBuilder = $params['form_builder']; 
        $langagueId = $this->context->language->id;

        $formBuilder->add(
            'shortdesc',
            \PrestaShopBundle\Form\Admin\Type\FormattedTextareaType::class,
            [
                'limit' => 500,
                'label' => $this->l('Short Description'),
                'required' => false
            ]
        );

        $category = new Category((int) $params['id']);
        $params['data']['shortdesc'] = $category->shortdesc[$langagueId];
        
        $formBuilder->setData($params['data']);
    }


    public function hookActionAfterCreateCategoryFormHandler(array $params)
    {
        $this->updateData($params['id'], $params['form_data']);
    }


    public function hookActionAfterUpdateCategoryFormHandler(array $params)
    {
        $this->updateData($params['id'], $params['form_data']);
    }


    protected function updateData(int $id_category, array $data)
    {

        
        $cat = new Category((int)$id_category);
        $cat->shortdesc =  $data['shortdesc'] ;
        $cat->update();
    }
}
