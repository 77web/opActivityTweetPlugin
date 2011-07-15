<?php

class opActivityTweetPluginConfiguration extends sfPluginConfiguration
{
  public function initialize()
  {
    //hack member config
    $this->dispatcher->connect('op_action.pre_execute_member_config', array($this, 'listenToPreMemberConfig'));
    
    //manual post
    $this->dispatcher->connect('op_action.post_execute_member_updateActivity', array($this, 'listenToManualActivityPost'));
  }
  
  public function listenToPreMemberConfig($arguments)
  {
    $request = sfContext::getInstance()->getRequest();
    $user = sfContext::getInstance()->getUser();
    $member = $user->getMember();
    
    if('activityTweet' == $request->getParameter('category') && (''==$member->getConfig('twitter_oauth_token') || ''==$member->getConfig('twitter_oauth_token_secret')))
    {
      $callbackToken = $request->getParameter('oauth_token');
      $callbackVerifier = $request->getParameter('oauth_verifier');
      
      $oauth = opActivityTweet::getInstance();
      
      if($callbackToken && $callbackVerifier)
      {
        $requestToken = $user->getAttribute('request_token');
        if(isset($requestToken['oauth_token']) && 0 === strcmp($callbackToken, $requestToken['oauth_token']))
        {
          $oauth = opActivityTweet::getInstance($requestToken['oauth_token'], $requestToken['oauth_token_secret']);
          $token = $oauth->getAccessToken($callbackVerifier);
          if(isset($token['oauth_token']) && isset($token['oauth_token_secret']))
          {
            $member->setConfig('twitter_oauth_token', $token['oauth_token']);
            $member->setConfig('twitter_oauth_token_secret', $token['oauth_token_secret']);
            $member->setConfig('twitter_user_id', $token['user_id']);
            $member->setConfig('twitter_screen_name', $token['screen_name']);
          }
        }
      }
      elseif($request->isMethod(sfRequest::POST))
      {
        $requestToken = $oauth->getRequestToken($request->getUri());
        $user->setAttribute('request_token', $requestToken);
        
        //redirect to twitter authorize
        $arguments['actionInstance']->redirect($oauth->getAuthorizeURL($requestToken));
      }
    }
  }
  
  public function listenToManualActivityPost($arguments)
  {
    $form = $arguments['actionInstance']->getVar('form');
    if($form->isValid())
    {
      $member = sfContext::getInstance()->getUser()->getMember();
      $activity = $form->getObject();
      if(ActivityDataTable::PUBLIC_FLAG_OPEN == $activity->getPublicFlag())
      {
        opActivityTweet::tweetMember($member, $activity->getBody());
      }
    }
  }
}