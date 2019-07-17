<?php
namespace Sigma\Grid\Admin;

use \JqGrider\Grid;
use \JqGrider\Grid\Column;
use \Sigma\Collection\Grid\Reply as CollectionGridJob;
use \Zend\Json\Expr as JsonExpr;

class Reply extends Grid {

    public function init() {
        /*
            https://packagist.org/packages/djuki/jqgrider
            https://github.com/cpttripzz/jqgrider
            https://github.com/vkislichenko/jqgrider
         */
        $urlConfig = new \stdClass();
        $urlConfig->data = '/admin/sigma/reply/gridData';
//        $urlConfig->add = '/admin/sigma/reply/add';
//        $urlConfig->edit = '/admin/sigma/reply/edit/<rowId>';
//        $urlConfig->delete = '/admin/sigma/reply/delete/<rowId>';


        $this
            ->setGridIdentifier('#grid')
            ->setPagerDivIdentifier('#gridPager')
            ->setDataType(Grid::DATA_TYPE_JSON)
            ->setUrl($urlConfig->data)
            ->setRepository(new CollectionGridJob())
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
                'repositoryAttribute' => 'credit_request_id',
                'title' => 'Credit Request',
                'width' => 80,
                'searchOptions' => [
                    'enable' => true,
                ],
            ])
            ->addColumn([
                'align' => Column::ALIGN_CENTER,
                'repositoryAttribute' => 'type',
                'title' => 'Type',
                'width' => 80,
                'searchOptions' => [
                    'enable' => true,
                ],
            ])
            ->addColumn([
                'align' => Column::ALIGN_CENTER,
                'repositoryAttribute' => 'file',
                'title' => 'File',
                'width' => 200,
                'searchOptions' => [
                    'enable' => true,
                ],
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
                'repositoryAttribute' => 'surname',
                'title' => 'Surname',
                'width' => 100,
                'searchOptions' => [
                    'enable' => true,
                ],
            ])
            ->addColumn([
                'align' => Column::ALIGN_CENTER,
                'repositoryAttribute' => 'birthday',
                'title' => 'Birthday',
                'width' => 60,
                'searchOptions' => [
                    'enable' => true,
                ],
            ])
//            ->addColumn([
//                'align' => Column::ALIGN_LEFT,
//                'repositoryAttribute' => 'answers',
//                'title' => 'Reply',
//                'width' => 80,
//                'callbackFunction' => function($cell, $row) {
//                    return sprintf(
//                        "A: %s\nB: %s\nC: %s",
//                        $row->getAnswerA(),
//                        $row->getAnswerB(),
//                        $row->getAnswerC()
//                    );
//                },
//            ])
            ->addColumn([
                'align' => Column::ALIGN_CENTER,
                'repositoryAttribute' => 'answerA',
                'title' => 'Answer A',
                'width' => 70,
                'searchOptions' => [
                    'enable' => true,
                ],
            ])
            ->addColumn([
                'align' => Column::ALIGN_CENTER,
                'repositoryAttribute' => 'answerB',
                'title' => 'Answer B',
                'width' => 70,
                'searchOptions' => [
                    'enable' => true,
                ],
            ])
            ->addColumn([
                'align' => Column::ALIGN_CENTER,
                'repositoryAttribute' => 'answerC',
                'title' => 'Answer C',
                'width' => 70,
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