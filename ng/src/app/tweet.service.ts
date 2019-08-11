import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Observable, of } from 'rxjs';
import { Tweet } from './tweet';
import { TWEETS } from './mock-tweets';
import { catchError, map, tap } from 'rxjs/operators';

@Injectable({
  providedIn: 'root'
})
export class TweetService {

  private tweetsUrl = '/twitter-search/tweets.json';

  constructor(private http: HttpClient) { }

  getTweets(query: string): Observable<Tweet[]> {
    // return of(TWEETS);
    let url = this.tweetsUrl +'?q='+ encodeURIComponent(query);
    return this.http.get<Tweet[]>(url).pipe(
      catchError((error: any) => {
        console.error(error);
        return of( [] as Tweet[] );
      })
    );
  }

}
