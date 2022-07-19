<?php

/**
 * CE Packagist Info bundle for Contao Open Source CMS
 *
 * @author    Benny Born <benny.born@numero2.de>
 * @license   LGPL-3.0-or-later
 * @copyright Copyright (c) 2022, numero2 - Agentur für digitales Marketing GbR
 */


namespace numero2\CEPackagistInfoBundle\EventListener\DataContainer;

use Contao\Database;


class ContentListener {


    /**
     * Purges the stored packagist data if the name is changed
     *
     * @param mixed $varValue
     * @param DataContainer $dc
     *
     * @return string
     */
    public function updatePackagistName( $varValue, $dc ): string {

        if( !empty($varValue) && $varValue != $dc->activeRecord->packagist_name ) {
            Database::getInstance()->prepare("UPDATE ".$dc->table." SET packagist_data = NULL WHERE id = ?")->execute($dc->id);
        }

        return $varValue;
    }
}