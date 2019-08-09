import { Component, OnInit } from '@angular/core';
import { TweetService } from '../tweet.service';
import { Tweet } from '../tweet';

@Component({
  selector: 'app-tweets',
  templateUrl: './tweets.component.html',
  styleUrls: ['./tweets.component.css']
})
export class TweetsComponent implements OnInit {

  query = '';
  hasSearched = false;
  isSearching = false;
  tweets: Tweet[];

  constructor(private tweetService: TweetService) { }

  ngOnInit() { }

  getTweets(): void {
    if (this.isSearching) { return; }
    this.query = this.query.replace(/^\s+|\s+$/g, '');
    if (!this.query.length) { return; }
    this.isSearching = true;
    this.hasSearched = true;
    this.tweetService.getTweets(this.query).subscribe(tweets => {
      for (let tweet of tweets) {
        tweet.created_at = this.timeSince(tweet.created_at);
      }
      tweets.sort(function(a, b) {
        return (b.retweet_count + b.favorite_count) - (a.retweet_count + a.favorite_count);
      });
      this.tweets = tweets;
      this.isSearching = false;
    });
  }

  private timeSince(dateStr : string): string {
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
