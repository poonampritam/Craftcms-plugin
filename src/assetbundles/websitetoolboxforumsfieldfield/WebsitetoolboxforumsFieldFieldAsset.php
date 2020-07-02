<?php
/**
 * websitetoolboxforums plugin for Craft CMS 3.x
 *
 * test plugin
 *
 * @link      http://websitetoolbox.com
 * @copyright Copyright (c) 2020 Website Toolbox
 */

namespace websitetoolbox\websitetoolboxforums\assetbundles\websitetoolboxforumsfieldfield;

use Craft;
use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

/**
 * WebsitetoolboxforumsFieldFieldAsset AssetBundle
 *
 * AssetBundle represents a collection of asset files, such as CSS, JS, images.
 *
 * Each asset bundle has a unique name that globally identifies it among all asset bundles used in an application.
 * The name is the [fully qualified class name](http://php.net/manual/en/language.namespaces.rules.php)
 * of the class representing it.
 *
 * An asset bundle can depend on other asset bundles. When registering an asset bundle
 * with a view, all its dependent asset bundles will be automatically registered.
 *
 * http://www.yiiframework.com/doc-2.0/guide-structure-assets.html
 *
 * @author    Website Toolbox
 * @package   Websitetoolboxforums
 * @since     1.0.0
 */
class WebsitetoolboxforumsFieldFieldAsset extends AssetBundle
{
    // Public Methods
    // =========================================================================

    /**
     * Initializes the bundle.
     */
    public function init()
    {
        // define the path that your publishable resources live
        $this->sourcePath = "@websitetoolbox/websitetoolboxforums/assetbundles/websitetoolboxforumsfieldfield/dist";

        // define the dependencies
        $this->depends = [
            CpAsset::class,
        ];

        // define the relative path to CSS/JS files that should be registered with the page
        // when this asset bundle is registered
        $this->js = [
            'js/WebsitetoolboxforumsField.js',
        ];

        $this->css = [
            'css/WebsitetoolboxforumsField.css',
        ];

        parent::init();
    }
}
