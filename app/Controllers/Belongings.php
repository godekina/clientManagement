<?php

namespace App\Controllers;

use App\Libraries\Crud;


class Belongings extends BaseController
{
	protected $crud;

	function __construct()
	{
		$params = [
			'table' => 'belongings',
			'dev' => false,
			'fields' => $this->field_options(),
			'form_title_add' => 'Add Belongings',
			'form_title_update' => 'Edit Belongings',
			'form_submit' => 'Add',
			'table_title' => 'Belongings',
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
        $columns = ['b_cid',
					'b_collected_by',
					'b_status',
					'b_date',
                    ];
		$where = null;//['u_status =' => 'Active'];
		$order = [
			['b_id', 'ASC']
		];
		$data['table'] = $this->crud->view($page, $per_page, $columns, $where, $order);
		return view('admin/belongings/table', $data);
	}

	public function add(){
		
		$data['form'] = $form = $this->crud->form();
		$data['title'] = $this->crud->getAddTitle();

		if(is_array($form) && isset($form['redirect']))
			return redirect()->to($form['redirect']);

		return view('admin/belongings/form', $data);
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
		
		return view('admin/belongings/form', $data);
	}


	protected function field_options()
	{
		$fields = [];
        $fields['b_id'] = ['label' => 'ID'];
        $fields['b_cid']=[
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
  
       
		$fields['b_list'] = ['label' => 'List of Items','required' => true,'helper'=>'Type in Item collected, description and quantity. eg Belt, Brown 2 pcs. Press ENTER for next item','type' => 'editor'];
		$fields['b_date'] = ['label' => 'Date & Time', 'required' => true, 'class' => 'col-12 col-sm-4'];
		$fields['b_collected_by'] = ['label' => 'Collected by', 'required' => true, 'class' => 'col-12 col-sm-6'];
		$fields['b_status'] = ['label' => 'Status', 'required' => true, 'class' => 'col-12 col-sm-6'];
		return $fields;
	}

    public function removeFeaturedImage($parent_id)
	{
		$crud = $this->crud;
		$current_values = $crud->current_values($parent_id);
		if (!$current_values)
			return redirect()->to($crud->getBase() . '/' . $crud->getTable());

		$fileColumnName = 'p_image';
		$field = $crud->getFields($fileColumnName);
		
		$table = $crud->getTable();
		$data = [$fileColumnName => ''];
		$where = [$crud->get_primary_key_field_name() => $parent_id];
		$affected = $crud->updateItem($table, $where, $data);
		
		if (!$affected)
		$crud->flash('warning', 'File could not be deleted');
		else {

			if ($field['delete_file'] ?? false && $field['delete_file'] === TRUE)
				unlink($field['path'] . '/' .  $current_values->{$fileColumnName});

			$crud->flash('success', 'File was deleted');
		}

		$url = $crud->getBase() . '/' . $crud->getTable() . '/edit/' . $parent_id;
		return redirect()->to($url);
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
