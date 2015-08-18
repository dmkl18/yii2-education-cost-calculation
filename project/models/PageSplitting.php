<?php

namespace app\models;

use Yii;
use yii\data\Pagination;
use yii\db\Query;
use app\models\interfaces\IDataPages;

class PageSplitting
{

	private $query;
	private $pages;
	
	/*
		$query - объект класса Query или производного от него ActiveQuery
		$countMaterialOnPage - количество материалов на странице
		$page - текущая страница
	*/
	public function __construct(IDataPages $data, $countMaterialOnPage, $page)
	{
        $query = $data->getAllByPage();
		$this->query = $query;
		$countQuery = clone $query;
		/*
			pageParam = 'page'  - параметр, который берется из Get-запроса для формирования постраничного разбиения (по умолчанию page)
			pageSizeParam == false - в URL количество материалов на странице не отображается
			forcePageParam == false - в самой пагинации для первой страницы номер страницы не выводится
		*/
		$this->pages = new Pagination(['totalCount' => $countQuery->count(), 'pageSize' => (int) $countMaterialOnPage, 'pageSizeParam' => false]);
		$this->checkPage($this->pages->totalCount, (int) $page, (int) $countMaterialOnPage);
	}
	
	/*
		$order - если данный параметр равен '', то используется произвольный порядок либо же порядок, который уже был установлен в $this->query
		$asArray - необходим для определения типа возвращаемых данных в том случае, если $this->query - объект класса ActiveQuery
	*/
	public function getCurrentMaterial($order = '', $asArray = false)
	{
		if($order) {
			$this->query = $this->query->orderBy($order);
		}
		$fullQuery = $this->query
			->offset($this->pages->offset)
			->limit($this->pages->limit);
		return ($asArray) ? $fullQuery->asArray()->all() : $fullQuery->all();
	}
	
	//возвращаем объект класса yii\data\Pagination
	public function getPagination()
	{
		return $this->pages;
	}
	
	/*
		В том случае, если не выполнять данную проверку, при отображении страницы, номер которой превышает допустимый в данный момент
			будет просто отображаться последняя страница
		С выполнением данной проверки в таком случае будет выброшено исключение
	*/
	private function checkPage($total, $page, $countMaterialOnPage)
	{
		if(($total && $page * $countMaterialOnPage - $total >= $countMaterialOnPage) || (!$total && $page!==1))
			throw new \yii\web\NotFoundHttpException();
	}

}