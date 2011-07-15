<?php

class MemberConfigActivityTweetForm extends MemberConfigForm
{
  protected $category = 'activityTweet';
  
  public function configure()
  {
    if('' == sfContext::getInstance()->getUser()->getMember()->getConfig('twitter_oauth_token'))
    {
      sfContext::getInstance()->getResponse()->addStylesheet('/opActivityTweetPlugin/css/oauth.css');
    }
  }
}