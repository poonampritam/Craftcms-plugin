<?php
/**
 * Websitetoolboxforums plugin for Craft CMS 3.x
 *
 * Single Sign On plugin for WebsitetoolboxForums/jsConnect and CraftCMS
 *
 * @link      https://websitetoolbox.com/
 * @copyright Copyright (c) 2019 websitetoolbox
 */

namespace websitetoolbox\websitetoolboxforums\models;

use websitetoolbox\websitetoolboxforums\Websitetoolboxforums;

use craft\base\Model;
use craft\behaviors\EnvAttributeParserBehavior;

use yii\behaviors\AttributeTypecastBehavior;

/**
 * @author    websitetoolbox
 * @package   Websitetoolboxforums
 * @since     3.0.0
 */
class Settings extends Model
{
    // Public Properties
    // =========================================================================

    /**
     * @var string Websitetoolbox Forums jsConnect Client ID
     */
    public $forumUsername = '';

    /**
     * @var string Websitetoolbox Forums jsConnect Secret
     */
    public $forumPassword = '';

    /**
     * @var string The hash algorithm to be ued when signing requests
     */
    public $forumEmbedded = '';

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['forumUsername', 'forumPassword','forumEmbedded'], 'string'],
            [['forumUsername', 'forumPassword','forumEmbedded'], 'default', 'value' => ''],
            
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        // Keep any parent behaviors
        $behaviors = parent::behaviors();
        // Add in the AttributeTypecastBehavior
        $behaviors['typecast'] = [
            'class' => AttributeTypecastBehavior::class,
            // 'attributeTypes' will be composed automatically according to `rules()`
        ];
        // If we're running Craft 3.1 or later, add in the EnvAttributeParserBehavior
        if (Websitetoolboxforums::$craft31) {
            $behaviors['parser'] = [
                'class' => EnvAttributeParserBehavior::class,
                'attributes' => [
                    'forumUsername',
                    'forumPassword',
                    'forumEmbedded'
                ],
            ];
        }

        return $behaviors;
    }
}
