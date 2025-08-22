<?php

namespace App\Libraries;

/**
 * Pagination Library
 * 
 * This class is used for generating pagination data.
 * 
 * @package     App\Libraries
 * @version     1.0
 * @developer   Pradeep Yadav
 * @created     04-11-2023
 * @authorized  Pradeep Yadav
 * @comments    This class is used for site message
 * @contact     softkiller706@gmail.com
 */
class Pagination
{
	/**
	 * Generate pagination data.
	 * 
	 * @param int $totalRecords Total number of records.
	 * @param int $currentPage  Current page number.
	 * @param int $limit Number of records per page.
	 * 
	 * @return array Pagination data.
	 */
	public function getPaginate(int $totalRecords = 0, int $currentPage = 1, int $limit = 50): array
	{
		$pagination = [];

		// Total number of records
		$pagination['totalRecords'] = $totalRecords;

		// Current page number
		$pagination['currentPage'] = $currentPage;

		// Number of records per page
		$pagination['recordsPerPage'] = $limit;

		// First index on the current page
		$pagination['firstIndex'] = (($currentPage - 1) * $limit) + 1;

		// Last index on the current page
		$pagination['lastIndex'] = min((($currentPage - 1) * $limit) + $limit, $totalRecords);

		// Whether pagination is needed
		$pagination['hasPagination'] = $totalRecords > $limit;

		// Next page number
		$pagination['nextPage'] = $currentPage + 1;

		// Previous page number
		$pagination['previousPage'] = $currentPage - 1;

		// First page number
		$pagination['firstPage'] = 1;

		// Last page number
		$pagination['lastPage'] = (int) ceil($totalRecords / $limit);

		// Page links to display
		$pagination['links'] = $this->generatePageLinks($currentPage, $pagination['lastPage'], 50);

		return $pagination;
	}

	/**
	 * Generate page links for pagination.
	 * 
	 * @param int $currentPage   Current page number.
	 * @param int $lastPage      Last page number.
	 * @param int $linksToShow   Number of links to show.
	 * 
	 * @return array Page links.
	 */
	private function generatePageLinks(int $currentPage, int $lastPage, int $linksToShow): array
	{
		$pageLinks = [];

		if ($currentPage % $linksToShow != 0) {
			$quotient = (int) floor($currentPage / $linksToShow) + 1;
			$lastLink = $quotient * $linksToShow;
			$firstLink = $lastLink - ($linksToShow - 1);
		} else {
			$quotient = (int) ($currentPage / $linksToShow);
			$lastLink = $quotient * $linksToShow;
			$firstLink = $lastLink - ($linksToShow - 1);
		}

		$i = $firstLink;
		while ($i <= $lastLink) {
			if ($i > $lastPage) {
				break;
			}

			$pageLinks[] = $i;
			$i++;
		}

		return $pageLinks;
	}
}
