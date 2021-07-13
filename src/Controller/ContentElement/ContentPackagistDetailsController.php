<?php

/**
 * CE Packagist Info bundle for Contao Open Source CMS
 *
 * @author    Benny Born <benny.born@numero2.de>
 * @license   LGPL-3.0-or-later
 * @copyright Copyright (c) 2021, numero2 - Agentur für digitales Marketing GbR
 */


namespace numero2\CEPackagistInfoBundle\Controller\ContentElement;

use Contao\ContentModel;
use Contao\CoreBundle\Controller\ContentElement\AbstractContentElementController;
use Contao\CoreBundle\ServiceAnnotation\ContentElement;
use Contao\System;
use Contao\Template;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


/**
 * @ContentElement
 */
class ContentPackagistDetailsController extends AbstractContentElementController {


    /**
     * @inheritdoc
     */
    protected function getResponse( Template $template, ContentModel $model, Request $request ): ?Response {

        if( !$model->packagist_data ) {
            self::updatePackageData($model);
        }

        if( $model->packagist_data ) {

            $oData = null;
            $oData = json_decode($model->packagist_data);

            // find the most recent version
            if( $oData->package->versions ) {

                $latestVersion = null;

                foreach( $oData->package->versions as $v ) {

                    if( substr_compare($v->version_normalized, 'dev-', 0, 4) === 0 ) {
                        continue;
                    }

                    if( !$latestVersion || strtotime($v->time) > strtotime($latestVersion->time) ) {
                        $latestVersion = $v;
                    }

                }

                if( $latestVersion ) {

                    $template->version = $latestVersion->version;
                    $template->contao = ($latestVersion->require->{'contao/core-bundle'}?:$latestVersion->require->{'contao-community-alliance/composer-plugin'})??null;
                }
            }

            $template->stars = $oData->package->github_stars;
            $template->downloads = $oData->package->downloads->total;

        } else {

            return new Response('');
        }

        return $template->getResponse();
    }


    /**
     * Update the data stored for the current package
     *
     * @return boolean
     */
    public static function updatePackageData( ContentModel $model ): bool {

        $oClient = null;
        $oClient = HttpClient::create();

        $oResponse = null;
        $oResponse = $oClient->request('GET', 'https://packagist.org/packages/'.$model->packagist_name.'.json');

        if( $oResponse->getStatusCode() === 200 ) {

            $oContent = null;
            $oContent = $oResponse->getContent();

            if( $oContent ) {

                $model->packagist_data = $oContent;

                if( $model->save() ) {

                    System::log('Updated packagist data for package "'.$model->packagist_name.'"', __METHOD__, TL_GENERAL);
                    return true;
                }
            }

        } else if( $oResponse->getStatusCode() === 404 ) {

            System::log('Could not update data for package "'.$model->packagist_name.'" - 404 not found', __METHOD__, TL_ERROR);
            return false;
        }

        System::log('Could not update data for package "'.$model->packagist_name.'" - unknown error', __METHOD__, TL_ERROR);
        return false;
    }
}
