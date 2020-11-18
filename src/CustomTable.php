<?php

namespace Dytechltd\CustomTable;

use Illuminate\Pagination\LengthAwarePaginator;
use Laravel\Nova\Card;

class CustomTable extends Card
{
	public static $instanceCount = 0;

	/**
	 * The width of the card (1/3, 1/2, or full).
	 *
	 * @var string
	 */
	public $width = 'full';

	public function __construct(array $header = [], array $data = [], string $title = '')
	{
		parent::__construct();

		self::$instanceCount++;

		$this->withMeta([
			'header' => $this->_convertToArray($header),
			'rows' => $this->_convertToArray($data),
			'title' => $title,
		]);
	}

	private function _convertToArray(array $data): array
	{
		return collect($data)
			->map(function ($value) {
				return $value->toArray();
			})->toArray();
	}

	public function header(array $header)
	{
		return $this->withMeta([
			'header' => $this->_convertToArray($header)
		]);
	}

	public function data(array $data)
	{
		return $this->withMeta(['rows' => $this->_convertToArray($data)]);
	}

	public function title(string $title)
	{
		return $this->withMeta(['title' => $title]);
	}

	public function paginator(LengthAwarePaginator $paginator)
	{
		return $this->withMeta([
			'paginator' => $paginator,
		]);
	}

	public function configValues(array $config = [])
	{
		return $this->withMeta([
			'config' => $config,
		]);
	}

	function __destruct()
	{
		self::$instanceCount--;
	}

	/**
	 * Get the component name for the element.
	 *
	 * @return string
	 */
	public function component()
	{
		return 'custom-table';
	}
}
