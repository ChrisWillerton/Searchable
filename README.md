# Searchable

Searchable trait for Laravel Eloquent models.

This trait uses a full text search, along with a results collection to sort multiple model searches by relevance.


## Installation

Add the following to the `composer.json` file in your project:

    "chriswillerton/searchable": "dev-master"

or you can run the below on the command line in the root of your project:

    composer require "chriswillerton/searchable" "dev-master"


## Setup

To get started, add the trait to the model:

	use ChrisWillerton\Searchable\Searchable;

	class YourModel extends Eloquent
	{
		use Searchable;

		protected $full_text_index = 'title, content';

You also need to define the full text index that you've setup in your database.


## Usage

You can use searchable as below:

	$results = YourModel::search('search term')
		->orderBy('relevance', 'desc')
		->get();


The collection is used for searching multiple models that implement the searchable trait. Imagine you have an Article, Post, and Product class, but you want results from all of these mixed together and sorted by relevance. You could do something like the below:

	// Set which models we wish to search
	$models_to_search = [
		Article::class,
		Post::class,
		Product::class
	];

	$term = 'search term';
	$model_results = [];

	// Loop through each model and perform the search query
	foreach ($models_to_search as $model)
	{
		$model_results[] = $model::search($term)
			->get()
			->toArray();
	}

	// Merge each result set together into a single array of results
	$merged_results = array_merge(...$model_results);

	// Add these results to a collection...
	$collection = new ChrisWillerton\Searchable\SearchableResults($merged_results);

	// and order them by relevance
	$results = $collection->getByRelevance();

You should consider the performance impact of doing the above though. It's probably best to come up with a specific search solution if maximising performance is required.