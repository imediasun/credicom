<?php

namespace App\modules\Core\Collection\Grid;

use JqGrider\Data\IGridRepository;
use JqGrider\Data\Conditions;

class Base implements IGridRepository
{
    public $collectionClass = null;
    public $collection = null;
    public $foundRows = null;
    public $calcFoundRows = true;


    public function __construct()
    {
        if(!$this->collection) {
            $collectionClass = $this->collectionClass;



            $this->collection = $collectionClass::getInstance();
        }
    }

    public function getQueryParams(Conditions $dataConditions) {
        $result = $this->collection->normalizeQueryParams();

        //sort
        if($dataConditions->sortBy && $dataConditions->sort) {
            $result['sort'] = [$dataConditions->sortBy => $dataConditions->sort];
        }


        //filter
        if($dataConditions->search) {
            $result['filter'] = $dataConditions->searchConditions;
        }

        $limit = $dataConditions->rowsLimit;
        $offset = $dataConditions->page == 1 ? 0 : $dataConditions->rowsLimit * ($dataConditions->page - 1);
        $result['limit'] = sprintf('%s OFFSET %s', $limit, $offset);

//var_dump($dataConditions);
//var_dump($result);

        return $result;
    }

    public function getData(Conditions $dataConditions)
    {
        $queryParams = $this->getQueryParams($dataConditions);

        $result = $this->collection->getList($queryParams);
//var_dump($result); exit;
        $this->foundRows = $this->collection->foundRows;

        return $result;
    }

    public function countDataRows(Conditions $dataConditions)
    {
        return $this->foundRows;
    }
}