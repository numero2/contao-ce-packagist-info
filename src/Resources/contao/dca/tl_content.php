<?php

/**
 * CE Packagist Info bundle for Contao Open Source CMS
 *
 * @author    Benny Born <benny.born@numero2.de>
 * @license   LGPL-3.0-or-later
 * @copyright Copyright (c) 2022, numero2 - Agentur für digitales Marketing GbR
 */


/**
 * Add palettes to tl_content
 */
$GLOBALS['TL_DCA']['tl_content']['palettes']['packagist_details'] = '{type_legend},type,headline;{packagist_legend},packagist_name;{template_legend:hide},customTpl;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID;{invisible_legend:hide},invisible,start,stop';


/**
 * Add fields to tl_content
 */
$GLOBALS['TL_DCA']['tl_content']['fields']['packagist_name'] = [
    'inputType' => 'text'
,   'eval' => ['maxlength'=>255, 'tl_class'=>'w50']
,   'save_callback' => [ ['numero2_packagist_details.listener.data_container.content', 'updatePackagistName'] ]
,   'sql' => "varchar(255) NOT NULL default ''"
];
$GLOBALS['TL_DCA']['tl_content']['fields']['packagist_data'] = [
    'sql' => "mediumblob NULL"
];