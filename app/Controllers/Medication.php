<?php

namespace App\Controllers;

use App\Libraries\Crud;


class Medication extends BaseController
{
	protected $crud;

	function __construct()
	{
		$params = [
			'table' => 'medication',
			'dev' => false,
			'fields' => $this->field_options(),
			'form_title_add' => 'Add Medication',
			'form_title_update' => 'Edit Medication',
			'form_submit' => 'Add',
			'table_title' => 'Medication',
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
					'm_cid',
					'm_name',
					'm_dosage',
					'm_route',
					'm_start_date',
					'm_status',
                    ];
		$where = null;//['u_status =' => 'Active'];
		$order = [
			['m_id', 'ASC']
		];
		$data['table'] = $this->crud->view($page, $per_page, $columns, $where, $order);
		return view('admin/medication/table', $data);
	}

	public function add(){
		
		$data['form'] = $form = $this->crud->form();
		$data['title'] = $this->crud->getAddTitle();

		if(is_array($form) && isset($form['redirect']))
			return redirect()->to($form['redirect']);

		return view('admin/medication/form', $data);
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
		
		return view('admin/medication/form', $data);
	}


	protected function field_options()
	{
		$fields = [];
        $fields['m_id'] = ['label' => 'ID'];
        $fields['m_cid']=[
            'label'=>'Medication For',
            'required'=> true,
            'type'=>'dropdown',
            'disable'=>true,
            'relation'=>[
                'table'=>'clients',
                'primary_key'=>'c_id',
                'display'=>['c_firstname', 'c_surname'],
                'order_by'=>'c_firstname',
                'order'=> 'ASC'
                    ]
				]; 
		$fields['client_files']=[
			'label'=>'Client Files',
			'type'=>'files',
			'files_relation'=>[
				'files_table'=>'client_files',
				'primary_key'=>'cf_id',
				'parent_field'=>'cf_client_id',
				'file_name_field'=>'cf_file_name',
				'file_type_field'=>'cf_file_type'
			],

			'path'=>'./uploads/images',
			//'is_image'=>true,
			'max_size'=>'4096',
			//'ext_in'=>'png,jpg,gif',
			'wrapper_start'=>'<div class="row">',
			'wrapper_end'=>'</div>',
			'wrapper_item_start'=>'<div class="col-6 col-sm-3 mt-3 mb-3">',
			'wrapper_item_end'=>'</div>',
			'show_file_names'=>true,
			'placeholder'=>'/admin/assets/img/file-icon.png',
			'delete_callback'=>'deleteFile',
			'delete_file'=>true,
			'delete_button_class'=>'btn btn-danger btn-xs',
			'helper'=>'Insert files here: e.g. Prescription, Lab Test Results',
						];         
		
  
             
		$fields['m_name'] = ['label' => 'Medication Name', 'helper'=>'e.g: Risperdal 2mg, Diazepam 50mg ','required' => true, 'class' => 'col-12 col-sm-6'];
		$fields['m_dosage'] = ['label' => 'Dosage', 'plcaeholder'=>'Placeholder','helper'=>'e.g: 1 Tablet/ twice daily, 1 Amopule/thrice daily', 'required' => true, 'class' => 'col-12 col-sm-6'];
		$fields['m_route'] = ['label' => 'Administration Route', 'required' => true, 'class' => 'col-12 col-sm-6'];
		$fields['m_start_date'] = ['label' => 'Start Date', 'required' => true, 'class' => 'col-12 col-sm-6'];
		$fields['m_end_date'] = ['label' => 'End Date', 'required' => false, 'class' => 'col-12 col-sm-6'];
		$fields['m_status'] = ['label' => 'Status', 'required' => true, 'class' => 'col-12 col-sm-6'];
		return $fields;
	}

    
    public function deletefile($parent_id, $file_id)
	{
		$crud = $this->crud;
		$current_values = $crud->current_values($parent_id);
		if (!$current_values)
			return redirect()->to($crud->getBase() . '/' . $crud->getTable());

        $field = $crud->getFields('client_files');    
		$table = $field['files_relation']['files_table'];
		
		$relationOptions = $field['files_relation'];
		$where = [$relationOptions['primary_key'] => $file_id];
		$item = $crud->deleteItem($table, $where);

		if(!$item)
			$crud->flash('warning', 'File could not be deleted');
		else{
			
			if( $field['delete_file'] ?? false && $field['delete_file'] === TRUE)
				unlink($field['path'].'/'. $parent_id.'/'. $item->{$relationOptions['file_name_field']});
			
			$crud->flash('success', 'File was deleted');
		}
		
		$url = $crud->getBase() . '/' . $crud->getTable() . '/edit/' . $parent_id;
		return redirect()->to($url);
	}
    //--------------------------------------------------------------------

}
