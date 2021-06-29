<?php

namespace App\Controllers;

use App\Libraries\Crud;


class Case_notes extends BaseController
{
	protected $crud;

	function __construct()
	{
		$params = [
			'table' => 'case_notes',
			'dev' => false,
			'fields' => $this->field_options(),
			'form_title_add' => 'Add Case Note',
			'form_title_update' => 'Edit Case Note',
			'form_submit' => 'Add',
			'table_title' => 'Case Note',
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
        $columns = ['cn_cid',
					'cn_created_by',
					'cn_created_at',
                    ];
		$where = null;//['u_status =' => 'Active'];
		$order = [
			['cn_id', 'ASC']
		];
		$data['table'] = $this->crud->view($page, $per_page, $columns, $where, $order);
		return view('admin/case_notes/table', $data);
	}

	public function add(){
		
		$data['form'] = $form = $this->crud->form();
		$data['title'] = $this->crud->getAddTitle();

		if(is_array($form) && isset($form['redirect']))
			return redirect()->to($form['redirect']);

		return view('admin/case_notes/form', $data);
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
		
		return view('admin/case_notes/form', $data);
	}


	protected function field_options()
	{
		$fields = [];
        $fields['cn_id'] = ['label' => 'ID'];
        $fields['cn_cid']=[
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
			'helper'=>'Attach Case Notes',
						];         
				
  
       
		$fields['cn_created_by'] = ['label' => 'Counselor','required' => true,  'class' => 'col-12 col-sm-6'];
		$fields['cn_created_at'] = ['label' => 'Date & Time','required' => true,  'class' => 'col-12 col-sm-6'];
		$fields['cn_complains'] = ['label' => 'Complains', 'required' => true, 'class' => 'col-12 col-sm-6'];
		$fields['cn_requests'] = ['label' => 'Requests', 'required' => true, 'class' => 'col-12 col-sm-6'];
		$fields['cn_priviledges'] = ['label' => 'Priviledges', 'required' => true, 'class' => 'col-12 col-sm-6'];
		$fields['cn_observations'] = ['label' => 'Observations', 'required' => true, 'class' => 'col-12 col-sm-6'];
		return $fields;
	}
    
    public function deletefile($parent_id, $file_id)
	{
		$crud = $this->crud;
		$current_values = $crud->current_values($parent_id);
		if (!$current_values)
			return redirect()->to($crud->getBase() . '/' . $crud->getTable());

        $field = $crud->getFields('project_files');    
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
