<?php
/**
 * Websitetoolboxforums plugin for Craft CMS 3.x
 *
 * Single Sign On plugin for WebsitetoolboxForums/jsConnect and CraftCMS
 *
 * @link      https://websitetoolbox.com/
 * @copyright Copyright (c) 2019 websitetoolbox
 */

namespace websitetoolbox\websitetoolboxforums\variables;
echo "ddd";exit;
use websitetoolbox\websitetoolboxforums\Websitetoolboxforums;

use craft\helpers\Template;

/**
 * @author    websitetoolbox
 * @package   Websitetoolboxforums
 * @since     3.0.0
 */
class WebsitetoolboxforumsVariable
{
    // Public Methods
    // =========================================================================

    /**
     * Generate an SSO string suitable for passing in the url for embedded SSO
     *
     * @param int $userId
     *
     * @return string
     */
    public function embeddedOutput(int $userId = 0): string
    {
        return Template::raw(
            Websitetoolboxforums::$plugin->sso->embeddedOutput($userId)
        );
    }

    /**
     * Return whether we are running Craft 3.1 or later
     *
     * @return bool
     */
    public function craft31(): bool
    {
        return Websitetoolboxforums::$craft31;
    }
}
