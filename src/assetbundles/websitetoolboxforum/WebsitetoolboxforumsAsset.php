<?php
/**
 * Website Toolbox Forum plugin for Craft CMS 3.x
 *
 * Single Sign On plugin for Website ToolboxForums  and CraftCMS
 *
 * @link      https://websitetoolbox.com/
 * @copyright Copyright (c) 2019 websitetoolbox
 */

namespace websitetoolbox\websitetoolboxforums\assetbundles\websitetoolboxforums;
use Craft;
use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

/**
 * @author    websitetoolbox
 * @package   Websitetoolboxforums
 * @since     3.0.0
 */
class WebsitetoolboxforumsAsset extends AssetBundle
{
    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */

    public function init()
    {
        $this->sourcePath = "@websitetoolbox/websitetoolboxforums/assetbundles/websitetoolboxforums/dist";
         $this->depends = [
            CpAsset::class,
        ];
         
        $this->js = [
            'js/Websitetoolboxforums.js',
              
        ];
        
        parent::init();
    }
}

