<?php
namespace App\modules\Cron\Grid\Admin;

use \JqGrider\Grid;
use \JqGrider\Grid\Column;
use \App\modules\Cron\Collection\Grid\Job as CollectionGridJob;
use \Zend\Json\Expr as JsonExpr;
use App\Cron as CronModel;
class Cron extends Grid {

    public function init() {
        /*
            https://packagist.org/packages/djuki/jqgrider
            https://github.com/cpttripzz/jqgrider
            https://github.com/vkislichenko/jqgrider
         */
        $urlConfig = new \stdClass();
        $urlConfig->data = '/admin/cron/manager/gridDataAction';
        $urlConfig->add = '/admin/cron/manager/add';
        $urlConfig->edit = '/admin/cron/manager/edit/<rowId>';
        $urlConfig->delete = '/admin/cron/manager/delete/<rowId>';


        $this
            ->setGridIdentifier('#grid')
            ->setPagerDivIdentifier('#gridPager')
            ->setDataType(Grid::DATA_TYPE_JSON)
            ->setUrl($urlConfig->data)
            ->setRepository(new CollectionGridJob() ) //CronModel()
            ->setRowsPerPage(5)
            ->setGridComplete($urlConfig->data)
            ->setActionsNavOptions([
                'delbutton' => false,
                'editbutton' => false,

                'customDelicon' => "ui-icon-trash",
                'customDeltitle' => "Delete",

                'customEditicon' => "ui-icon-pencil",
                'customEdittitle' => "Edit",

                'custom' => [
                    [
                        'action' => "customEdit",
                        'onClick' => new JsonExpr('gridOpenPage'),
                        'display' => true,
                        'url' => $urlConfig->edit,
                        'target' => '_self',
                    ],

//                    [
//                        'action' => "customDel",
//                        'onClick' => new JsonExpr('gridOpenPage'),
//                        'display' => true,
//                        'url' => $urlConfig->delete,
//                        'target' => '_self',
//                    ],
                ],
            ])
        ;

        $this
            ->addColumn([
                'title' => 'ID',
                'repositoryAttribute' => 'id',
                'width' => 40,
            ])
            ->addColumn([
                'align' => Column::ALIGN_CENTER,
                'repositoryAttribute' => 'name',
                'title' => 'Name',
                'width' => 100,
                'searchOptions' => [
                    'enable' => true,
                ],
            ])
            ->addColumn([
                'align' => Column::ALIGN_CENTER,
                'repositoryAttribute' => 'enabled',
                'title' => 'Enabled',
                'width' => 100,
                'formatter' => "checkbox",
                'searchType' =>  "select",
                'searchOptions' => [
                    'sopt' => ["eq", "eq"],
                    'value' => ":Any;1:Yes;0:No",
                ],
            ])
            ->addColumn([
                'align' => Column::ALIGN_CENTER,
                'repositoryAttribute' => 'status',
                'title' => 'Status',
                'width' => 100,
                'searchOptions' => [
                    'enable' => true,
                ],
            ])
            ->addColumn([
                'align' => Column::ALIGN_CENTER,
                'repositoryAttribute' => 'interval',
                'title' => 'Interval',
                'width' => 100,
                'searchOptions' => [
                    'enable' => true,
                ],
            ])
            ->addColumn([
                'align' => Column::ALIGN_CENTER,
                'repositoryAttribute' => 'last_run',
                'title' => 'Last Run',
                'width' => 200,
                'searchOptions' => [
                    'enable' => true,
                ],
            ])
            ->addColumn([
                'align' => Column::ALIGN_CENTER,
                'repositoryAttribute' => 'actions',
                'title' => 'Actions',
                'width' => 100,
                'formatter' => 'actions'
            ])
        ;

//        $this->setCustomNavButtons([
//            [
//                'id' => 'add-new-item',
//                'formatter' => 'href',
//                'caption' => "Add New Item",
//                'buttonicon' => "ui-icon-plusthick",
//                'onClickButton' => new JsonExpr('gridPanelOpenPage'),
//                'position' => "last",
//                'title' =>"",
//                'cursor' => "pointer",
//                'url' => $urlConfig->add,
//            ]
//        ]);
    }
}