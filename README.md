# Twitter Search

* Given a search term (in a Google-like search box), queries Twitter's API to find the 1,000 most recent posts with this keyword.
* The results are displayed in descending order of Retweets + Favourites.
* Clicking on the text of a post opens the original post on twitter.com.
* Clicking on a username opens their profile on twitter.com.

## Installation

1. Clone this repository.
2. Run `composer install` and `npm install` at the project root folder.
3. Copy `config/app.default.php` as `config/app.php`. Then fill in the salt (line 79) and Twitter credentials (lines 396-399).
4. Update the base URL as necessary in `webroot/index.html:6`.
5. Update the `tweetsURL` variable as necessary in the two `webroot/main-es...js` files.

You can now either use your machine's webserver to open the project, or start up the built-in webserver with:

```bash
bin/cake server -p 8765
```

And then open `http://localhost:8765`.

## Installation with no SSH and no URL Rewritting

1. Download this repository.
2. Run `composer install` and `npm install` at the project root folder.
3. Upload all files to your server. The `ng` folder can be ommited, because the repository already contains the compiled Angular application in the `webroot` folder.
4. Fix file and folder permissions as necessary.
5. Follow [these instructions](https://book.cakephp.org/3.0/en/installation.html#i-can-t-use-url-rewriting) to disable URL rewriting on CakePHP.
6. Replace `src/Controller/TweetsController.php:24-27` with `exit(json_encode($tweets));`.
7. Update the base URL as necessary in `webroot/index.html:6`.
8. Update the `tweetsURL` variable as necessary in the two `webroot/main-es...js` files.

## References

* [CakePHP 3.8](https://book.cakephp.org/3.0/en/installation.html)
* [Angular 8.2.1](https://angular.io/guide/setup-local)
* [Twitter Standard Search API](https://developer.twitter.com/en/docs/tweets/search/api-reference/get-search-tweets.html)
