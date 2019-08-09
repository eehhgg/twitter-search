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
    return this.http.get<Tweet[]>( this.tweetsUrl +'?q='+ query ).pipe(
      catchError((error: any) => {
        console.error(error);
        return of( [] as Tweet[] );
      })
    );
  }

}
