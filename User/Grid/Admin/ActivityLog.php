<?php
namespace User\Grid\Admin;

use \JqGrider\Grid;
use \JqGrider\Grid\Column;
use \User\Collection\Grid\ActivityLog as CollectionGridActivityLog;
use \Zend\Json\Expr as JsonExpr;

class ActivityLog extends Grid {

    public function init() {
        /*
            https://packagist.org/packages/djuki/jqgrider
            https://github.com/cpttripzz/jqgrider
            https://github.com/vkislichenko/jqgrider
         */
        $urlConfig = new \stdClass();
        $urlConfig->data = '/admin/user/activity-log/gridData';
//        $urlConfig->add = '/admin/cron/manager/add';
//        $urlConfig->edit = '/admin/cron/manager/edit/<rowId>';
//        $urlConfig->delete = '/admin/cron/manager/delete/<rowId>';


        $this
            ->setGridIdentifier('#grid')
            ->setPagerDivIdentifier('#gridPager')
            ->setDataType(Grid::DATA_TYPE_JSON)
            ->setUrl($urlConfig->data)
            ->setRepository(new CollectionGridActivityLog())
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
//                    [
//                        'action' => "customEdit",
//                        'onClick' => new JsonExpr('gridOpenPage'),
//                        'display' => true,
//                        'url' => $urlConfig->edit,
//                        'target' => '_self',
//                    ],

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
                'repositoryAttribute' => 'date',
                'title' => 'Date',
                'width' => 100,
                'searchOptions' => [
                    'enable' => true,
                ],
            ])
            ->addColumn([
                'align' => Column::ALIGN_CENTER,
                'repositoryAttribute' => 'ip',
                'title' => 'IP',
                'width' => 80,
                'searchOptions' => [
                    'enable' => true,
                ],
            ])
            ->addColumn([
                'align' => Column::ALIGN_CENTER,
                'repositoryAttribute' => 'user_agent',
                'title' => 'User Agent',
                'width' => 200,
                'searchOptions' => [
                    'enable' => true,
                ],
            ])
            ->addColumn([
                'align' => Column::ALIGN_CENTER,
                'repositoryAttribute' => 'session',
                'title' => 'Session',
                'width' => 120,
                'searchOptions' => [
                    'enable' => true,
                ],
            ])
            ->addColumn([
                'align' => Column::ALIGN_LEFT,
                'repositoryAttribute' => 'url',
                'title' => 'URL',
                'width' => 200,
                'searchOptions' => [
                    'enable' => true,
                ],
            ])
            ->addColumn([
                'align' => Column::ALIGN_CENTER,
                'repositoryAttribute' => 'method',
                'title' => 'Method',
                'width' => 40,
                'searchOptions' => [
                    'enable' => true,
                ],
            ])
            ->addColumn([
                'align' => Column::ALIGN_LEFT,
                'repositoryAttribute' => 'data',
                'title' => 'Data',
                'width' => 200,
                'searchOptions' => [
                    'enable' => true,
                ],
            ])
//            ->addColumn([
//                'align' => Column::ALIGN_CENTER,
//                'repositoryAttribute' => 'actions',
//                'title' => 'Actions',
//                'width' => 100,
//                'formatter' => 'actions'
//            ])
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