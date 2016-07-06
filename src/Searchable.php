<?php

namespace ChrisWillerton\Searchable;

trait Searchable
{
	public static function bootSearchable(){}

	public function scopeSearch($query, $term)
	{
		if ($this->full_text_index)
		{
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
		}

		return $query;
	}

	protected function prepareTerm($term)
	{
		// Keep any full text operators, as well as "word characters"
		return trim(preg_replace('/[^+\-><\(\)~*\"@\w\s]+/', '', $term));
	}
}