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
use craft\web\View;
use craft\services\Config;
 /** @noinspection MissingPropertyAnnotationsInspection */

/**
 * @author    websitetoolbox
 * @package   Websitetoolboxforums
 * @since     3.0.0
 */
class Sso extends Component
{
    
    function afterLogin(){  
       $token = Craft::$app->getSession()->get(Craft::$app->getUser()->tokenParam); 
        if($token){ 
             $forumApiKey = Craft::$app->getProjectConfig()->get('plugins.websitetoolboxforums.settings.forumApiKey');
             $forumUrl = Craft::$app->getProjectConfig()->get('plugins.websitetoolboxforums.settings.forumUrl');
            if($forumApiKey){ 
                $myUserQuery = \craft\elements\User::find();
                $userEmail = Craft::$app->getUser()->getIdentity()->email;
                $userId= Craft::$app->getUser()->getIdentity()->id;
                $userName= Craft::$app->getUser()->getIdentity()->username;
                $RequestUrl = $forumUrl."/register/setauthtoken";
                $postData = array('type'=>'json','apikey' => $forumApiKey, 'user' => $userName,'email'=>$userEmail,'externalUserid'=>$userId);
                 $ch = curl_init();
                curl_setopt($ch,CURLOPT_URL,$RequestUrl);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
                curl_setopt($ch,CURLOPT_POSTFIELDS,json_encode($postData));
                curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
                'Content-Type: application/json','Accept: application/json'));      
                curl_setopt($ch, CURLOPT_HEADER, 0);
                $response = curl_exec($ch);
                $result = json_decode($response, true); 
                setcookie("forumLogoutToken", $result['authtoken'], time() + 3600,"/");
                setcookie("forumLoginUserid", $result['userid'], time() + 3600,"/");

                
            }
        }
         
     }
    function afterUserCreate($userId){ 
       echo $forumUrl = Craft::$app->getProjectConfig()->get('plugins.websitetoolboxforums.settings.forumUrl',false);
       echo $forumApiKey = Craft::$app->getProjectConfig()->get('plugins.websitetoolboxforums.settings.forumApiKey',false); 
        $postData = array( 'type'=>'json','apikey'=>$forumApiKey,'member' => $_POST['username'],
        'externalUserid' => $userId, 
        'email' => $_POST['email']);
        if(isset($_POST['firstName'])){
           $postData['name'] =  $_POST['firstName'];
        }
        if(isset($_POST['firstName'])){
           $postData['name'] .=  " ".$_POST['lastName'];
        }
        
        $RequestUrl = $forumUrl . "/register/create_account/";
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,$RequestUrl);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch,CURLOPT_POSTFIELDS,json_encode($postData));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
        'Content-Type: application/json','Accept: application/json'));      
        curl_setopt($ch, CURLOPT_HEADER, 0);
        $response = curl_exec($ch); 
        $result = json_decode($response, true);
    }
  function afterUpdateUser(){
  //Websitetoolboxforums::getInstance()->sso->updateUserDetails();  
  $emailToVerify  = $_SESSION['userEmailBeforeUpdate'];
  $userId     = Websitetoolboxforums::getInstance()->sso->getUserid($emailToVerify);
  $userName = $_POST['username'];
  $externalUserid = $_POST['userId'];
  $email = $_POST['email'];;
  $userDetails = array("type"=>"json","email" => $email,
          "username" => $userName,
          "externalUserid" => $externalUserid,
          "name" => $userName);

  $url =  "/users/$userId";
  $response = Websitetoolboxforums::getInstance()->sso->sendApiRequest('POST',$url,$userDetails);
 }
function getUserid($userEmail){
     if ($userEmail) {
        $data     = array(
            "email" => $userEmail
        );
        $response = Websitetoolboxforums::getInstance()->sso->sendApiRequest('GET', "/users/", $data); 
        if ($response->{'data'}[0]->{'userId'}) {
             return $response->{'data'}[0]->{'userId'};
        }
    }
}
function sendApiRequest($method, $path, $data){
    $forumApiKey = Craft::$app->getProjectConfig()->get('plugins.websitetoolboxforums.settings.forumApiKey',false);
    $url = "https://api.websitetoolbox.com/dev/api" . $path;
    if ($method == "GET") {
        echo $url = sprintf("%s?%s", $url, http_build_query($data));
    }
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array(
        "x-api-key: " . $forumApiKey,
        'Content-Type: application/json'
    ));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    if ($method == "POST") {
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
    } else if ($method == "GET") {
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    }
    $response = curl_exec($curl);
    curl_close($curl);
    return json_decode($response);
}
    function afterDeleteUser($userName){        
       $forumApiKey = Craft::$app->getProjectConfig()->get('plugins.websitetoolboxforums.settings.forumApiKey',false);
       $forumUrl = Craft::$app->getProjectConfig()->get('plugins.websitetoolboxforums.settings.forumUrl',false);
       $postData         = array('type'=>'json','apikey' => $forumApiKey,'massaction' => 'decline_mem','usernames' => $userName);
       $RequestUrl =  $forumUrl."/register";
       $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,$RequestUrl);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch,CURLOPT_POSTFIELDS,json_encode($postData));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
        'Content-Type: application/json','Accept: application/json'));      
        curl_setopt($ch, CURLOPT_HEADER, 0);
        $response = curl_exec($ch); 
        $result = json_decode($response, true);
    }
    function afterLogOut(){  
      echo '<img src="https://beta.websitetoolbox.com/register/logout?authtoken='.$_COOKIE['forumLogoutToken'].'" border="0" width="0" height="0" alt="" id="imageTag">'; 
      
     
     }
     function serviceMethod() 
    {   

        $oldMode = \Craft::$app->view->getTemplateMode();
\Craft::$app->view->setTemplateMode(View::TEMPLATE_MODE_CP);
$html = \Craft::$app->view->renderTemplate('websitetoolboxforums/index');
\Craft::$app->view->setTemplateMode($oldMode);
    }
}
