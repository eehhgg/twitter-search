<?php

namespace App\Controller;

use Cake\Core\Configure;
use Abraham\TwitterOAuth\TwitterOAuth;

class TweetsController extends AppController {

  /**
   * Initialize Controller
   */
  public function initialize() {
    parent::initialize();
    $this->loadComponent('RequestHandler');
  }

  /**
   * API endpoint that returns recent tweets that contain a given search query.
   */
  public function index() {
    $query = $this->sanitizeQuery( $this->request->getQuery('q') );
    $tweets = $this->getTweets($query);
    $this->set([
        'tweets' => $tweets,
        '_serialize' => 'tweets'
    ]);
  }

  /* Private functions */

  /**
   * Sanitizes a search query so it complies with Twitter specifications.
   * @param string $query
   */
  private function sanitizeQuery($query) {
    $query = trim($query);
    // trim to 500 characters, without breaking the last 1-byte encoded character (like %3D)
    if ( mb_strlen($query) <= 500 ) { return $query; }
    if ($query[498] === '%') { $query = mb_substr($query, 0, 498); }
    elseif ($query[499] === '%') { $query = mb_substr($query, 0, 499); }
    else { $query = mb_substr($query, 0, 500); }
    return $query;
  }

  /**
   * Uses the Twitter API to get 1k recent tweets that contain a given search query.
   * @param string $query
   */
  private function getTweets($query) {
    if ( empty($query) ) {
      $this->writeLog('Empty query');
      return [];
    }
    $conf = Configure::read('Twitter');
    $connection = new TwitterOAuth( $conf['consumer_key'], $conf['consumer_secret'], $conf['access_token'], $conf['access_token_secret'] );
    $connection->setTimeouts(30, 30);
    $nPerPage = 100;
    $nPages = 10;
    $params = [
      'q' => $query,
      'result_type' => 'recent',
      'count' => $nPerPage,
      'tweet_mode' => 'extended'
    ];
    $tweets = [];

    for ($i = 1; $i <= $nPages; $i++) {
      // get tweets
      $search = $connection->get('search/tweets', $params);
      if ( $connection->getLastHttpCode() != 200
        || empty($search->statuses)
        || empty($search->search_metadata->max_id_str) ) {
        $this->writeLog( 'Error response: ' . $connection->getLastHttpCode() );
        $this->writeLog($search);
        break;
      }
      $nextTweets = $this->formatTweets($search->statuses);
      if (!$nextTweets) { break; }
      $tweets = array_merge($tweets, $nextTweets);
      // update max_id parameter
      $lastIdStr = $nextTweets[count($nextTweets) - 1]['id'];
      $maxIdStr = $this->decStrNum($lastIdStr);
      if (!$maxIdStr) {
        $this->writeLog( 'Invalid maxIdStr: ' . $maxIdStr . ' for ' . $lastIdStr );
        break;
      }
      $params['max_id'] = $maxIdStr;
    }

    return $tweets;
  }

  /**
   * Selects the necessary fields from an array of tweets.
   * @param array $tweets
   */
  private function formatTweets($tweets) {
    if ( empty($tweets) || !is_array($tweets) ) {
      $this->writeLog('Empty tweets');
      $this->writeLog($tweets);
      return null;
    }
    $fTweets = [];
    foreach ($tweets as $t) {

      // validate tweet
      if ( !isset( $t->created_at, $t->id_str, $t->full_text, $t->user,
        $t->user->screen_name, $t->retweet_count, $t->favorite_count ) ) {
        $this->writeLog('Missing tweet field');
        $this->writeLog($t);
        continue;
      }
      // get fields
      $fTweets[] = [
        'created_at' => $t->created_at,
        'id' => $t->id_str,
        'text' => html_entity_decode($t->full_text, ENT_HTML5),
        'user_screen_name' => html_entity_decode($t->user->screen_name, ENT_HTML5),
        'retweet_count' => $t->retweet_count,
        'favorite_count' => $t->favorite_count
      ];

    }
    return $fTweets;
  }

  /**
   * Decreases by one a string that represents a positive integer.
   * @param string $n
   */
  private function decStrNum($n) {
    $n = trim((string) $n);
    if ( ((int) $n == 0) || !preg_match('/\d+/', $n) ) { return null; }
    $result = $n;
    $len = strlen($n);
    $i = $len - 1;
    $completed = false;
    while ($i > -1) {
      if ($n[$i] === "0") {
        $result[$i] = "9";
        $i--;
      } else {
        $result[$i] = ((int) $n[$i]) - 1;
        return $result;
      }
    }
    return $result;
  }

  /**
   * Writes an object to a log file.
   * @param mixed $obj
   */
  private function writeLog($obj) {
    $this->log($obj, 'debug');
  }

}
