<?php

namespace ChrisWillerton\Searchable;

use Illuminate\Database\Eloquent\Collection;

class SearchableResults extends Collection
{
	public function getByRelevance()
	{
		return $this->sortByDesc(function($item)
		{
			return $item['relevance'];
		});
	}
}