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
echo "fff";exit;
use websitetoolbox\websitetoolboxforums\Websitetoolboxforums;

use craft\base\Model;
use craft\behaviors\EnvAttributeParserBehavior;

use yii\behaviors\AttributeTypecastBehavior;

/**
 * @author    websitetoolbox
 * @package   Websitetoolboxforums
 * @since     3.0.0
 */
class SsoData extends Model
{
    // Public Properties
    // =========================================================================

    /**
     * @var int Unique ID for this user
     */
    public $uniqueid;

    /**
     * @var string Display name for this user
     */
    public $name;

    /**
     * @var string Email address for this user
     */
    public $email;

    /**
     * @var string Ootional comma-delimited roles for this user
     */
    public $roles;

    /**
     * @var string URL to a photo for this user
     */
    public $photourl;

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uniqueid'], 'integer'],
            [['uniqueid'], 'default', 'value' => 0],
            [['name'], 'string'],
            [['name'], 'default', 'value' => ''],
            [['email'], 'email'],
            [['email'], 'default', 'value' => ''],
            [['roles'], 'string'],
            [['roles'], 'default', 'value' => ''],
            [['photourl'], 'url'],
            [['photourl'], 'default', 'value' => ''],
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
                ],
            ];
        }

        return $behaviors;
    }
}
