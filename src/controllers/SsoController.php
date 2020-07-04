<?php
/**
 * Websitetoolboxforums plugin for Craft CMS 3.x
 *
 * Single Sign On plugin for WebsitetoolboxForums/jsConnect and CraftCMS
 *
 * @link      https://websitetoolbox.com/
 * @copyright Copyright (c) 2019 websitetoolbox
 */

namespace websitetoolbox\websitetoolboxforums\controllers;
echo "lll";exit;
use websitetoolbox\websitetoolboxforums\Websitetoolboxforums;

use craft\web\Controller;

/**
 * @author    websitetoolbox
 * @package   Websitetoolboxforums
 * @since     3.0.0
 */
class SsoController extends Controller
{
    // Protected Properties
    // =========================================================================

    /**
     * @var    bool|array Allows anonymous access to this controller's actions.
     *         The actions must be in 'kebab-case'
     * @access protected
     */
    protected $allowAnonymous = ['output'];

    // Public Methods
    // =========================================================================

    /**
     * Generate the jsConnect string for single sign on
     *
     * @param int $userId
     *
     * @throws \yii\base\ExitException
     */
    public function actionOutput(int $userId = 0)
    { 
        Websitetoolboxforums::$plugin->sso->output($userId);
    }
     public function actionSaveSso(bool $duplicate = false)
     {
            echo "vvvvvvvvv";exit;
     }
}
