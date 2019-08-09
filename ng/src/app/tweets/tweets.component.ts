import { Component, OnInit } from '@angular/core';
import { TWEETS } from '../mock-tweets';
import { Tweet } from '../tweet';

@Component({
  selector: 'app-tweets',
  templateUrl: './tweets.component.html',
  styleUrls: ['./tweets.component.css']
})
export class TweetsComponent implements OnInit {

  tweets = TWEETS;

  constructor() { }

  ngOnInit() {
    for (let tweet of this.tweets) {
      tweet.created_at = this.timeSince(tweet.created_at);
      tweet.popularity = tweet.retweet_count + tweet.favorite_count;
    }
    this.tweets.sort(function(a, b) { return b.popularity - a.popularity; });
  }

  timeSince(dateStr : string) : string {
    let date = +new Date(dateStr);
    let now = +new Date();
    let seconds = Math.floor((now - date) / 1000);
    let interval = Math.floor(seconds / 31536000);
    if (interval > 1) {
      return interval + " years";
    }
    interval = Math.floor(seconds / 2592000);
    if (interval > 1) {
      return interval + " months";
    }
    interval = Math.floor(seconds / 86400);
    if (interval > 1) {
      return interval + " days";
    }
    interval = Math.floor(seconds / 3600);
    if (interval > 1) {
      return interval + " hours";
    }
    interval = Math.floor(seconds / 60);
    if (interval > 1) {
      return interval + " minutes";
    }
    return Math.floor(seconds) + " seconds";
  }

}
