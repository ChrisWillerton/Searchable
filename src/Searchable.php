<?php

namespace ChrisWillerton\Searchable;

use ChrisWillerton\Searchable\SearchableFullTextException;

trait Searchable
{
	public static function bootSearchable(){}

	public function scopeSearch($query, $term)
	{
		if (!$this->full_text_index)
		{
			throw new SearchableFullTextException('Missing $full_text_index property.');
		}

		$term = $this->prepareTerm($term);

		$query->select([
			'*',
			\DB::raw("MATCH(" . $this->full_text_index . ") AGAINST (?) AS relevance")
		])
		->whereRaw("
			MATCH(" . $this->full_text_index . ") AGAINST(? IN BOOLEAN MODE)
		");

		$query->setBindings(
			array_merge([$term, $term], $query->getBindings())
		);

		return $query;
	}

	protected function prepareTerm($term)
	{
		// Remove anything that isn't a word or a space character
		$term = trim(preg_replace('/[^\w\s]+/', ' ', $term));

		if ($term)
		{
			// Add + operator for each word, meaning the word must be present
			$term = '+' . preg_replace('/\s+/', ' +', $term);
		}

		return $term;
	}
}