<?php
/**
 * Websitetoolboxforums plugin for Craft CMS 3.x
 *
 * Single Sign On plugin for WebsitetoolboxForums/jsConnect and CraftCMS
 *
 * @link      https://websitetoolbox.com/
 * @copyright Copyright (c) 2019 websitetoolbox
 */

namespace websitetoolbox\websitetoolboxforums\services;
use websitetoolbox\websitetoolboxforums\models\Settings;
use websitetoolbox\websitetoolboxforums\Websitetoolboxforums;
use websitetoolbox\websitetoolboxforums\events\SsoDataEvent;
use websitetoolbox\websitetoolboxforums\models\SsoData;

use Craft;
use craft\base\Component;
echo "vvvvvvvvvvvvvvv";exit;
 
/** @noinspection MissingPropertyAnnotationsInspection */

/**
 * @author    websitetoolbox
 * @package   Websitetoolboxforums
 * @since     3.0.0
 */
class Sso extends Component
{
    // Constants
    // =========================================================================

    /**
     * @event SsoDataEvent The event that is triggered before the SSO data is used,
     * you may modify the [[SsoDataEvent::data]] as you see fit. You may set
     * [[SsoDataEvent::isValid]] to `false` to prevent SSO data from being used.
     *
     * ```php
     * use websitetoolbox\websitetoolboxforums\services\Sso;
     * use websitetoolbox\websitetoolboxforums\events\SsoDataEvent;
     *
     * Event::on(Sso::class,
     *     SsoDataEvent::EVENT_SSO_DATA,
     *     function(SsoDataEvent $event) {
     *         // potentially set $event->isValid or modify $event->data
     *     }
     * );
     * ```
     */
    const EVENT_SSO_DATA = 'websitetoolboxForumsSsoData';

    // Public Methods
    // =========================================================================

    /**
     * Generate the jsConnect string for single sign on
     *
     * @param int $userId
     *
     * @return string
     * @throws \yii\base\ExitException
     */
    public function output(int $userId = 0): string
    { echo "vvvvvvvvvv";exit;
        $result = '';
        $settings = $this->getPluginSettings();
        $ssoData = $this->getSsoData($userId);
        if ($ssoData !== null) {
            $request = Craft::$app->getRequest();
            //ob_start(); // Start output buffering
            \WriteJsConnect(
                $ssoData->toArray(),
                $request->get(),
                $settings->forumUsername,
                $settings->forumPassword,
                $settings->forumEmbedded,
               
            );
            //$result = ob_get_contents();
            //ob_end_clean(); // Store buffer in variable
        }

        Craft::$app->end();
        //return $result === false ? '' : $result;
    }

    /**
     * Generate an SSO string suitable for passing in the url for embedded SSO
     *
     * @param int $userId
     *
     * @return string
     */
    public function embeddedOutput(int $userId = 0): string
    {
        $result = '';
        $settings = $this->getPluginSettings();
        $ssoData = $this->getSsoData($userId);
        if ($ssoData !== null) {
            $result = \JsSSOString(
                $ssoData->toArray(),
                $settings->forumUsername,
                $settings->forumPassword,
                $settings->forumEmbedded
            );
        }

        return $result;
    }

    // Private Methods
    // =========================================================================

    /**
     * Return an SSOData object filled in with the current user's info, or null
     *
     * @param int $userId
     *
     * @return SsoData|null
     */
    private function getSsoData(int $userId = 0)
    {
        $data = null;

        // Assume the currently logged in user if no $userId is passed in
        if ($userId === 0) {
            $user = Craft::$app->getUser()->getIdentity();
        } else {
            $users = Craft::$app->getUsers();
            $user = $users->getUserById($userId);
        }
        if ($user) {
            $generalConfig = Craft::$app->getConfig()->getGeneral();
            $name = $generalConfig->useEmailAsUsername ? $user->getFullName() : $user->username;
            $photoUrl = '';
            $photo = $user->getPhoto();
            if ($photo !== null) {
                $photoUrl = $photo->getUrl();
            }
            // Fill in the initial data
            $data = new SsoData([
                'uniqueid' => $user->id,
                'name' => $name,
                'email' => $user->email,
                'photourl' => $photoUrl,
            ]);
        }
        // Give plugins a chance to modify it
        $event = new SsoDataEvent([
            'user' => $user,
            'data' => $data,
        ]);
        $this->trigger(self::EVENT_SSO_DATA, $event);
        if (!$event->isValid) {
            return null;
        }

        return $data;
    }

    /**
     * Get the plugin's settings, parsing any environment variables
     *
     * @return Settings
     */
    private function getPluginSettings(): Settings
    {
        /** @var Settings $settings */
        $settings = Websitetoolboxforums::$plugin->getSettings();
        if (Websitetoolboxforums::$craft31) {
            $settings->forumUsername = Craft::parseEnv($settings->forumUsername);
            $settings->forumPassword = Craft::parseEnv($settings->forumPassword);
            $settings->forumEmbedded = Craft::parseEnv($settings->forumEmbedded);
             
        }

        return $settings;
    }
}
