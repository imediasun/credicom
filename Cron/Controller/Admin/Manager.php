<?php
namespace App\modules\Cron\Controller\Admin;

use \App\modules\Admin\Controller\Base as BaseAdminController;
use App\Http\Controllers\Controller;
use \App\modules\Cron\Grid\Admin\Cron as GridCron;
use \App\modules\Cron\Block\Admin\Job\Edit as EditForm;
use \App\modules\Cron\Collection\Job as CollectionJob;
use Illuminate\Http\Request;
use App\Http\Controllers\Admin\AdminFactory;
use App\Http\Controllers\Admin\AdminGeneral;
use App\Http\Controllers\Admin\SuperAdminEntity as AdminEntity;
class Manager extends  AdminEntity
{

    public function __construct(Request $request){
        //$this->middleware('breadcrumbs');
        //$this->f=new AdminFactory();
        $this->general=AdminGeneral::getInstance();
        $this->general->init($request);
        $this->request=$request;
        $this->menu_active='Cron';
    }

    public function listAction()
    {
dump(123);
       // $this->cron_info=$this->f->getClients($this->general->admin_type,$this->request)->showCron()->init($this->request);
        //TODO : http://www.ok-soft-gmbh.com/jqGrid/OK/CustomActionButton.htm
        $grid = new GridCron();
        $this->menu=$this->makeMenu();
        //dump($grid->getJavaScriptCode());
       /* $this->view->appendData(array(
            'gridConfig' => $grid->getJavaScriptCode(),
        ));


        $this->app->render('list.php');*/
        $result=[
            'gridConfig' => $grid->getJavaScriptCode(),
            'menu'=>$this->menu,
			'menu_active'=>$this->menu_active,
            'admin_type'=>$this->general->admin_type,
            'breadcrumbs'=>$this->request->get('breadcrumbs'),

        ];
       return view('admin.cron',$result);
    }

    public function gridDataAction()
    {
        $grid = new GridCron();
        $grid->printRespositoryData();
         exit;
    }

    public function editAction($id = null)
    {
        $collection = CollectionJob::getInstance();


        $entity = $collection->load($id);
        //$entity = Cron::where('id',$id);

        if(!$entity) $entity = new Cron();

        $form = new EditForm([
            'entity' => $entity,
        ]);

        $this->view->appendData(array(
            'form' => $form
        ));

        $this->app->render('edit.php');
    }

}