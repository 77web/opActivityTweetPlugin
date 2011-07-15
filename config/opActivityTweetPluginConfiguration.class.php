<?php

class opActivityTweetPluginConfiguration extends sfPluginConfiguration
{
  public function initialize()
  {
    //manual post
    $this->dispatcher->connect('op_action.post_execute_member_updateActivity', array($this, 'listenToManualActivityPost'));
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