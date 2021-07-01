<?php

/**
 * CE Packagist Info bundle for Contao Open Source CMS
 *
 * @author    Benny Born <benny.born@numero2.de>
 * @license   LGPL-3.0-or-later
 * @copyright Copyright (c) 2021, numero2 - Agentur für digitales Marketing GbR
 */


namespace numero2\CEPackagistInfoBundle\Cron;

use Contao\ContentModel;
use Contao\CoreBundle\Framework\ContaoFramework;
use numero2\CEPackagistInfoBundle\Controller\ContentElement\ContentPackagistDetailsController;


class Updater {


    private $framework;


    public function __construct(ContaoFramework $framework) {
        $this->framework = $framework;
    }


    /**
     * Updates the data of all content elements of type `packagist_details`
     */
    public function __invoke(string $scope): void {

        $this->framework->initialize();

        $oElements = null;
        $oElements = ContentModel::findByType('packagist_details');

        if( $oElements ) {

            while( $oElements->next() ) {

                ContentPackagistDetailsController::updatePackageData( $oElements->current() );
            }
        }
    }
}
