<?php
/**
 * Websitetoolboxforums plugin for Craft CMS 3.x
 *
 * Single Sign On plugin for WebsitetoolboxForums/jsConnect and CraftCMS
 *
 * @link      https://websitetoolbox.com/
 * @copyright Copyright (c) 2019 websitetoolbox
 */

namespace websitetoolbox\websitetoolboxforums;
use websitetoolbox\websitetoolboxforums\services\Sso as SsoService;
use websitetoolbox\websitetoolboxforums\models\Settings;
use websitetoolbox\websitetoolboxforums\assetbundles\Websitetoolboxforums\WebsitetoolboxforumsAsset;
 
use Craft;
use craft\base\Plugin;
use craft\web\twig\variables\CraftVariable;
use craft\helpers\UrlHelper;
use yii\base\Event;
use craft\db\Query;
use craft\db\Table;
use craft\helpers\Db;
use craft\elements\User;
use craft\events\ModelEvent;
use craft\services\Users;
use craft\web\View;
use craft\services\Config;
define('WT_SETTINGS_URL', 'https://beta.websitetoolbox.com/tool/members/mb/settings');
/**
 * Class Websitetoolboxforums
 *
 * @author    websitetoolbox
 * @package   Websitetoolboxforums
 * @since     3.0.0
 *
 * @property  SsoService $sso
 */
class Websitetoolboxforums extends Plugin
{
    // Static Properties
    // =========================================================================

    /**
     * @var Websitetoolboxforums
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
    public $connection;
 
    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
         public $controllerMap = [
        'default' => DefaultController::class,
    ];
    public function init()
    { 
        parent::init();  
        self::$plugin = $this;
        
        $this->setComponents([
            'sso' => \websitetoolbox\websitetoolboxforums\services\Sso::class,
        ]);


     

    self::$craft31 = version_compare(Craft::$app->getVersion(), '3.1', '>=');
    Event::on(\craft\services\Elements::class, \craft\services\Elements::EVENT_BEFORE_SAVE_ELEMENT, function(Event $event) {
        if ($event->element instanceof \craft\elements\User) {
            if($event->element->id){
                $usersService = Craft::$app->getUsers();
                $userDetailsBeforeUpdate = $usersService->getUserById($event->element->id);
                $_SESSION['userEmailBeforeUpdate'] = $userDetailsBeforeUpdate['email'];
            }
        }
      });
      $token = Craft::$app->getSession()->get(Craft::$app->getUser()->tokenParam);
      if(!$token && isset($_COOKIE['forumLogoutToken'])){
            Event::on(View::class, View::EVENT_END_BODY, function(Event $event) {
                $forumUrl = Craft::$app->getPlugins()->getStoredPluginInfo('websitetoolboxforums') ["settings"]["forumUrl"];
                echo '<img src='.$forumUrl.'/register/logout?authtoken='.$_COOKIE['forumLogoutToken'].'" border="0" width="0" height="0" alt="" id="imageTag">';
                Websitetoolboxforums::getInstance()->sso->resetCookieOnLogout();
        });
    }
    
    Event::on(View::class, View::EVENT_BEFORE_RENDER_TEMPLATE,function (Event $event) {
        $forumType = Craft::$app->getProjectConfig()->get('plugins.websitetoolboxforums.settings.forumEmbedded',false);
        $view = Craft::$app->getView();
        $view->registerAssetBundle(WebsitetoolboxforumsAsset::class);
        if($forumType == 1){
            $jsRender = Websitetoolboxforums::getInstance()->sso->renderJsScriptEmbedded();
         }else{
            $jsRender = Websitetoolboxforums::getInstance()->sso->renderJsScriptUnEmbedded();
        }
        $view = Craft::$app->getView();
        $view ->registerJs($jsRender);
    });
    Event::on(\craft\services\Elements::class, \craft\services\Elements::EVENT_AFTER_SAVE_ELEMENT, function(Event $event) {
        if ($event->element instanceof \craft\elements\User) {
            if(isset($_POST['userId'])){
               Websitetoolboxforums::getInstance()->sso->afterUpdateUser();
            }else{
                Websitetoolboxforums::getInstance()->sso->afterUserCreate($event);
            }
        }
    });

    Event::on(\craft\services\Elements::class, \craft\services\Elements::EVENT_AFTER_DELETE_ELEMENT, function(Event $event) {
        if ($event->element instanceof \craft\elements\User) {                           
                Websitetoolboxforums::getInstance()->sso->afterDeleteUser($event->element->username);
        }
    });  
    Event::on( \yii\base\Component::class, \craft\web\User::EVENT_AFTER_LOGIN, function(Event $event) {
        Websitetoolboxforums::getInstance()->sso->afterLogin();
    });
    Event::on( \yii\base\Component::class, \craft\web\User::EVENT_AFTER_LOGOUT, function(Event $event) {
        Websitetoolboxforums::getInstance()->sso->afterLogOut();
    });
        

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

    public function afterSaveSettings()    
    {   
        $userName = $_POST['settings']['forumUsername'];
        $userPassword = $_POST['settings']['forumPassword'];
        $postData = array('action' => 'checkPluginLogin', 'username' => $userName,'password'=>$userPassword);
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,WT_SETTINGS_URL);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch,CURLOPT_POSTFIELDS,http_build_query($postData));
        $response = curl_exec($ch); 
        $result = json_decode($response, true);  
         
         if(empty(Craft::$app->getPlugins()->getStoredPluginInfo('websitetoolboxforums') ["settings"]["forumUrl"])){  
           $affectedRows = Craft::$app->getDb()->createCommand()->insert('projectconfig',
           [ 'path'=> 'plugins.websitetoolboxforums.settings.forumUrl','value' => '"'.$result['forumAddress'].'"'],false)->execute();
         }elseif(Craft::$app->getPlugins()->getStoredPluginInfo('websitetoolboxforums') ["settings"]["forumUrl"] != $result['forumAddress']){  
            $affectedRows = Craft::$app->getDb()->createCommand()->update('projectconfig', 
            ['plugins.websitetoolboxforums.settings.forumUrl' => $result['forumAddress']], 'path == plugins.websitetoolboxforums.settings.forumUrl')->execute();;
         }
         if(empty(Craft::$app->getPlugins()->getStoredPluginInfo('websitetoolboxforums') ["settings"]["forumApiKey"])){
            $affectedRows = Craft::$app->getDb()->createCommand()->insert('projectconfig',
           [ 'path'=> 'plugins.websitetoolboxforums.settings.forumApiKey','value' => '"'.$result['forumApiKey'].'"'],false)->execute();
         }
       $forumData = array('type'=>'json',
       'action' => 'modifySSOURLs',
       'forumUsername' => $userName,
       'forumApikey'=>$result['forumApiKey'],
       'embed_page_url'=>Craft::$app->getProjectConfig()->get('plugins.websitetoolboxforums.settings.forumOutputUrl'),
       'login_page_url'=>Craft::$app->getProjectConfig()->get('plugins.websitetoolboxforums.settings.loginUrl'),
       'logout_page_url' => Craft::$app->getProjectConfig()->get('plugins.websitetoolboxforums.settings.logOutUrl'),
       'registration_url' => Craft::$app->getProjectConfig()->get('plugins.websitetoolboxforums.settings.userRegistrationUrl')); 
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,WT_SETTINGS_URL);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch,CURLOPT_POSTFIELDS,http_build_query($forumData));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
        'Content-Type: application/json','Accept: application/json'));      
        curl_setopt($ch, CURLOPT_HEADER, 0);
        $response = curl_exec($ch); 
        $result = json_decode($response, true); 
        $this->sso->afterLogin();
     }
    
      
}