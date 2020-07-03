<?php
/**
 * Vanillaforums plugin for Craft CMS 3.x
 *
 * Single Sign On plugin for VanillaForums/jsConnect and CraftCMS
 *
 * @link      https://websitetoolbox.com/
 * @copyright Copyright (c) 2019 websitetoolbox
 */

namespace websitetoolbox\websitetoolboxforums;

use websitetoolbox\websitetoolboxforums\services\Sso as SsoService;
use websitetoolbox\websitetoolboxforums\variables\VanillaforumsVariable;
use websitetoolbox\websitetoolboxforums\models\Settings;

use Craft;
use craft\base\Plugin;
use craft\web\twig\variables\CraftVariable;

use yii\base\Event;

/**
 * Class Vanillaforums
 *
 * @author    websitetoolbox
 * @package   Vanillaforums
 * @since     3.0.0
 *
 * @property  SsoService $sso
 */
class Vanillaforums extends Plugin
{
    // Static Properties
    // =========================================================================

    /**
     * @var Vanillaforums
     */
    public static $plugin;

    /**
     * @var bool
     */
    public static $craft31 = false;

    // Public Properties
    // =========================================================================

    /**
     * @var string
     */
    public $schemaVersion = '1.0.0';

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        self::$plugin = $this;

        self::$craft31 = version_compare(Craft::$app->getVersion(), '3.1', '>=');

        Event::on(
            CraftVariable::class,
            CraftVariable::EVENT_INIT,
            function (Event $event) {
                /** @var CraftVariable $variable */
                $variable = $event->sender;
                $variable->set('websitetoolboxforums', VanillaforumsVariable::class);
            }
        );

        Craft::info(
            Craft::t(
                'websitetoolboxforums',
                '{name} plugin loaded',
                ['name' => $this->name]
            ),
            __METHOD__
        );
    }

    // Protected Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    protected function createSettingsModel()
    {
        return new Settings();
    }

    /**
     * @inheritdoc
     */
    protected function settingsHtml(): string
    {
        // Set up the form controls for editing the connection.
        $hashTypes = hash_algos();
        $hashTypes = array_combine($hashTypes, $hashTypes);

        return Craft::$app->view->renderTemplate(
            'websitetoolboxforums/settings',
            [
                'settings' => $this->getSettings(),
                'hashTypes' => $hashTypes,
            ]
        );
    }
}
