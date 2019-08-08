<?php

namespace App\Controller;

use Cake\Core\Configure;
use Abraham\TwitterOAuth\TwitterOAuth;

class TweetsController extends AppController {

  public function initialize() {
    parent::initialize();
    $this->loadComponent('RequestHandler');
  }

  public function index($query) {
    $query = $this->sanitizeQuery($query);
    $tweets = $this->getTweets($query);
    $this->set([
        'tweets' => $tweets,
        '_serialize' => ['tweets']
    ]);
  }

  private function sanitizeQuery($q) {
    $q = mb_substr($q, 0, 500);
    return urlencode($q);
  }

  private function getTweets($query) {
    $conf = Configure::read('Twitter');
    $connection = new TwitterOAuth($conf['consumer_key'], $conf['consumer_secret'], $conf['access_token'], $conf['access_token_secret']);
    $tweets = $connection->get('search/tweets', [
      'q' => $query,
      'result_type' => 'recent',
      'count' => 100,
      //'max_id' => ,
      //'since_id' => 
    ]);
    return $tweets;
  }

}
