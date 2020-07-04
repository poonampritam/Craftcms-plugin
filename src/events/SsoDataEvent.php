<?php
/**
 * Websitetoolboxforums plugin for Craft CMS 3.x
 *
 * Single Sign On plugin for WebsitetoolboxForums/jsConnect and CraftCMS
 *
 * @link      https://websitetoolbox.com/
 * @copyright Copyright (c) 2019 websitetoolbox
 */

namespace websitetoolbox\websitetoolboxforums\events;
echo "jjj";exit;
use craft\elements\User;
use websitetoolbox\websitetoolboxforums\models\SsoData;

use yii\base\ModelEvent;

/**
 * @author    websitetoolbox
 * @package   Websitetoolboxforums
 * @since     3.0.0
 */
class SsoDataEvent extends ModelEvent
{
    // Properties
    // =========================================================================

    /**
     * @var User|null The user associated with this SSO data (usually the currently logged in user)
     */
    public $user;

    /**
     * @var SsoData|null the SsoData model
     */
    public $data;
}
