<?php

namespace App\Libraries;

use App\Libraries\Crud_core;
use CodeIgniter\HTTP\RequestInterface;

class Crud extends Crud_core
{
    function __construct($params, RequestInterface $request)
    {
        parent::__construct($params, $request);
    }

    function form()
    {
        return $this->parent_form();
    }
    public function callback_days_left($item){
        $days = $this->days($item->p_end_date);
        if ($days<0){
            $out = '<span class="text-success">Completed</span>';
        }else{
            $out = '<span>' .$days . ' days left</span>';
        }
        return $out;

    }
    public function days($date){
        $now = time();
        $your_date = strtotime($date);
        $date_diff = $your_date - $now;
        return round($date_diff/(60 * 60 * 24));
    }
    public function callback_featured_image($item){
        $src = $item->c_image ? '/uploads/images/'.$item->c_image : '/admin/assets/img/profile.png';
        return '<img src="'.$src.'" style ="width:70px;" class="img-circle">';

    }

}
