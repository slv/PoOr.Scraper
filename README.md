_PoOr.Scraper_ is a PHP Script that returns json string with Open Graph Metatags from a given URL.

# Example

	http://www.example.com/index.php?id=www.domaintoscrape.com

return:

	{
		"description": "URL Description",
		"title": "URL Title",
		"url": "URL",
		"img": [
			"http://www.domaintoscrape.com/logo.jpg",
			"http://www.domaintoscrape.com/head.jpg",
			"http://www.domaintoscrape.com/img_5.jpg",
			"http://www.anotherdomain.com/img.jpg"
		]
	}