<?php

namespace App\Controllers;

use App\Libraries\Crud;


class Drug_test extends BaseController
{
	protected $crud;

	function __construct()
	{
		$params = [
			'table' => 'drug_test',
			'dev' => false,
			'fields' => $this->field_options(),
			'form_title_add' => 'New Drug Test',
			'form_title_update' => 'Edit Drug Test',
			'form_submit' => 'Add',
			'table_title' => 'Drug Test',
			'form_submit_update' => 'Update',
			'base' => '',

		];

		$this->crud = new Crud($params, service('request'));
	}

	public function index()
	{
		$page = 1;
		if (isset($_GET['page'])) {
			$page = (int) $_GET['page'];
			$page = max(1, $page);
		}

		$data['title'] = $this->crud->getTableTitle();

        $per_page = 20;
        //'p_start_date', 'p_end_date', 'p_status',
        $columns = [
					'dt_cid',
					'dt_conducted_by',
					'dt_date_conducted',
                    ];
		$where = null;//['u_status =' => 'Active'];
		$order = [
			['dt_id', 'ASC']
		];
		$data['table'] = $this->crud->view($page, $per_page, $columns, $where, $order);
		return view('admin/drug_test/table', $data);
	}

	public function add(){
		
		$data['form'] = $form = $this->crud->form();
		$data['title'] = $this->crud->getAddTitle();

		if(is_array($form) && isset($form['redirect']))
			return redirect()->to($form['redirect']);

		return view('admin/drug_test/form', $data);
	}

	public function edit($id)
	{
		if(!$this->crud->current_values($id))
			return redirect()->to($this->crud->getBase() . '/' . $this->crud->getTable());

		$data['item_id'] = $id;
		$data['form'] = $form = $this->crud->form();
		$data['title'] = $this->crud->getEditTitle();

		if (is_array($form) && isset($form['redirect']))
			return redirect()->to($form['redirect']);
		
		return view('admin/drug_test/form', $data);
	}


	protected function field_options()
	{
		$fields = [];
        $fields['dt_id'] = ['label' => 'ID'];
        $fields['dt_cid']=[
            'label'=>'Client',
            'required'=> true,
            'type'=>'dropdown',
			'disable'=>true,
			'class'=>'col-12 col-sm-6',
            'relation'=>[
                'table'=>'clients',
                'primary_key'=>'c_id',
                'display'=>['c_firstname', 'c_surname'],
                'order_by'=>'c_firstname',
                'order'=> 'ASC'
                    ]
                ];
  
             
		$fields['dt_conducted_by'] = ['label' => 'Conducted By','required' => true, 'class' => 'col-12 col-sm-6'];
		$fields['dt_date_conducted'] = ['label' => 'Date & Time', 'required' => true, 'class' => 'col-12 col-sm-6'];
		$fields['dt_thc'] = ['label' => 'THC', 'required' => true,'option'=>'radio', 'class' => 'col-12 col-sm-2'];
		$fields['dt_cot'] = ['label' => 'COT', 'required' => true,'option'=>'radio', 'class' => 'col-12 col-sm-2'];	
		$fields['dt_bzo'] = ['label' => 'BZO', 'required' => true,'option'=>'radio', 'class' => 'col-12 col-sm-2'];		
		$fields['dt_ket'] = ['label' => 'KET', 'required' => true,'option'=>'radio', 'class' => 'col-12 col-sm-2'];
		$fields['dt_bar'] = ['label' => 'BAR', 'required' => true,'option'=>'radio', 'class' => 'col-12 col-sm-2'];	
		$fields['dt_opi'] = ['label' => 'OPI', 'required' => true,'option'=>'radio', 'class' => 'col-12 col-sm-2'];		
		$fields['dt_mdma'] = ['label' => 'MDMA', 'required' => true,'option'=>'radio', 'class' => 'col-12 col-sm-2'];
		$fields['dt_tml'] = ['label' => 'TML', 'required' => true,'option'=>'radio', 'class' => 'col-12 col-sm-2'];	
		$fields['dt_etg'] = ['label' => 'ETG', 'required' => true,'option'=>'radio', 'class' => 'col-12 col-sm-2'];		
		$fields['dt_coc'] = ['label' => 'COC', 'required' => true,'option'=>'radio', 'class' => 'col-12 col-sm-2'];
		$fields['dt_met'] = ['label' => 'MET', 'required' => true,'option'=>'radio', 'class' => 'col-12 col-sm-2'];	
		$fields['dt_alc'] = ['label' => 'ALC', 'required' => true,'option'=>'radio', 'class' => 'col-12 col-sm-2'];				
		return $fields;
	}

    
    
	
    //--------------------------------------------------------------------

}
