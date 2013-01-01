<?php

require_once(dirname(__FILE__).'/../vendor/twitteroauth/twitteroauth.php');

class opActivityTweet
{
  protected static $consumer_secret;
  protected static $consumer_key;
  
  protected static $connection;
  
  const POST_URI = 'https://twitter.com/statuses/update.xml';
  
  protected static function initialize()
  {
    self::$consumer_key = sfConfig::get('app_twitter_consumer_key');
    self::$consumer_secret = sfConfig::get('app_twitter_consumer_secret');
  }
  
  public static function getInstance($oauth_token = null, $oauth_secret = null)
  {
    if(empty(self::$consumer_key) || empty(self::$consumer_secret))
    {
      self::initialize();
    }
    
    return new TwitterOAuth(self::$consumer_key, self::$consumer_secret, $oauth_token, $oauth_secret);
  }
  
  protected static function setup($oauth_token, $oauth_secret)
  {
    if(empty(self::$consumer_key) || empty(self::$consumer_secret))
    {
      self::initialize();
    }
    self::$connection = new TwitterOAuth(self::$consumer_key, self::$consumer_secret, $oauth_token, $oauth_secret);
  }
  
  public static function tweetMember($member, $message)
  {
    if($member->getConfig('is_tweet_activity')==1 && ''!==$message)
    {
      $oauth_token = $member->getConfig('twitter_oauth_token');
      $oauth_secret = $member->getConfig('twitter_oauth_token_secret');
      
      self::setup($oauth_token, $oauth_secret);
      
      if(self::$connection)
      {
        $prefix = sfConfig::get('app_op_activity_tweet_plugin_tweet_prefix');
        if('' != $prefix)
        {
          $message = $prefix.' '.$message;
        }
        $suffix = sfConfig::get('app_op_activity_tweet_plugin_tweet_suffix');
        if('' != $suffix)
        {
          $message = $message.' '.$suffix;
        }
        self::$connection->post(self::POST_URI, array('status'=>$message, 'oauth_token'=>$oauth_token, 'oauth_secret'=>$oauth_secret));
        self::$connection = null;
      }
    }
  }
}