<?php
namespace App\modules\Cron\Block\Admin\Job;

use \App\modules\Core\Block\Form\Base as BaseForm;

class Edit extends BaseForm {

    public $template = 'admin/job/edit.php';




    public function valid()
    {
        $isValid = parent::valid(); //parent validation of subforms

        //validation
//        $this->validationErrors[] = ''


        return $isValid;
    }
}


