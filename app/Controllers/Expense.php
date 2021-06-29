<?php

namespace App\Controllers;

use App\Libraries\Crud;


class Expense extends BaseController
{
	protected $crud;

	function __construct()
	{
		$params = [
			'table' => 'expense',
			'dev' => false,
			'fields' => $this->field_options(),
			'form_title_add' => 'Add Expense',
			'form_title_update' => 'Edit Expense',
			'form_submit' => 'Add',
			'table_title' => 'Expenses',
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
        $columns = ['e_cid',
					'e_name',
					'e_balance',
					'e_date'
                    ];
		$where = null;//['u_status =' => 'Active'];
		$order = [
			['e_id', 'ASC']
		];
		$data['table'] = $this->crud->view($page, $per_page, $columns, $where, $order);
		return view('admin/expenses/table', $data);
	}

	public function add(){
		
		$data['form'] = $form = $this->crud->form();
		$data['title'] = $this->crud->getAddTitle();

		if(is_array($form) && isset($form['redirect']))
			return redirect()->to($form['redirect']);

		return view('admin/expenses/form', $data);
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
		
		return view('admin/expenses/form', $data);
	}


	protected function field_options()
	{
		$fields = [];
        $fields['e_id'] = ['label' => 'ID'];
        $fields['e_cid']=[
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
			'helper'=>'*Attach Reciepts if available',
						];         
				
  
       
		
		$fields['e_name'] = ['label' => 'Expense for', 'required' => true, 'helper'=>'e.g Provisions, Medication, Haircut','class' => 'col-12 col-sm-6'];
		$fields['e_date'] = ['label' => 'Date of Purchase', 'required' => true, 'class' => 'col-12 col-sm-4'];
		$fields['e_amount_spent'] = ['label' => 'Amount Spent', 'required' => true, 'class' => 'col-12 col-sm-4'];
		$fields['e_balance'] = ['label' => 'Balance', 'required' => true, 'class' => 'col-12 col-sm-4'];
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
