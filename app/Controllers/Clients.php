<?php
namespace App\Controllers;
use App\Libraries\Crud;
class Clients extends BaseController{
    protected $crud;

    function __construct(){
        $params =[
            'table'=>'clients',
            'dev'=>'false',
            'fields'=>$this->field_options(),
            'form_title_add'=>'Add Client',
            'form_title_update'=> 'Edit Client',
            'form_title_view'=> 'Client Info',
            'form_submit'=>'Add',
            'table_title'=>'Clients',
            'form_submit_update'=> 'Update',
            'base'=>'',

        ];
        $this->crud = new Crud($params, service('request'));
    }
    public function index(){

        $page = 1;
        if (isset($_GET['page'])){
            $page = (int)$_GET['page'];
            $page = max(1, $page);

        }

        $data['title']=$this->crud->getTableTitle();
        $per_page = 10;
        $columns = ['c_id',
                    ['label'=>'Image', 'callback'=>'callback_featured_image'], 
                    'c_firstname', 
                    'c_surname',
                    'c_admission_date',
                    'c_cid', 
                    ];
        $where = null;//['u_status ='=>'Active'];
        $order = [
            ['c_id', 'ASC']
        ];
        $data['table']=$this->crud->view($page, $per_page, $columns, $where, $order);
        return view('admin/clients/table', $data);
    }
    public function add(){
        $data['form'] = $form = $this->crud->form();
        $data['title'] = $this->crud->getAddTitle();

        if(is_array($form) && isset($form['redirect']))
            return redirect()->to($form['redirect']);
        return view('admin/clients/form', $data);
        }

    public function edit($id){
        if(!$this->crud->current_values($id)){
            return redirect()->to($this->crud->getBase() . '/' . $this->crud->getTable());
        }
        $data['item_id'] = $id;
        $data['form'] = $form = $this->crud->form();
        $data['title'] = $this->crud->getEditTitle();

        if(is_array($form) && isset($form['redirect']))
            return redirect()->to($form['redirect']);
        return view('admin/clients/form', $data);
        }
    public function view($id){
        if(!$this->crud->current_values($id)){
            return redirect()->to($this->crud->getBase() . '/' . $this->crud->getTable());
        }
        $data['item_id'] = $id;
        $data['form'] = $form = $this->crud->form();
        $data['title'] = $this->crud->getEditTitle();

        if(is_array($form) && isset($form['redirect']))
            return redirect()->to($form['redirect']);
        return view('admin/clients/form', $data);
        }
        
    protected function field_options(){
        $fields = [];

        $fields['c_id']=['label'=>'ID'];
        $fields['c_firstname']=['label'=>'First Name', 'required'=>true,'helper'=>'Type First Name', 'class'=>'col-12 col-sm-4'];
        $fields['c_surname']=['label'=>'Last Name', 'required'=>true, 'helper'=>'Type Surname', 'class'=>'col-12 col-sm-4'];
        $fields['c_othername']=['label'=>'Other Names', 'required'=>false, 'helper'=>'Type Other Names', 'class'=>'col-12 col-sm-4'];
        $fields['c_gender']=['label'=>'Gender', 'required'=>true, 'helper'=>'Type Other Names', 'class'=>'col-12 col-sm-3'];
        $fields['c_marital_status']=['label'=>'Marital Status', 'required'=>true, 'helper'=>'', 'class'=>'col-12 col-sm-3'];
        $fields['c_dob']=['label'=>'Date of Birth', 'required'=>true, 'helper'=>'', 'class'=>'col-12 col-sm-3'];
        $fields['c_age']=['label'=>'Age', 'required'=>true, 'helper'=>'Type Current Age', 'class'=>'col-12 col-sm-3'];
        $fields['c_occupation']=['label'=>'Occupation', 'required'=>true, 'helper'=>'Type Occupation', 'class'=>'col-12 col-sm-3'];
        $fields['c_religion']=['label'=>'Religion', 'required'=>true, 'helper'=>'', 'class'=>'col-12 col-sm-3'];
        $fields['c_nationality']=['label'=>'Nationality', 'required'=>true, 'helper'=>'Type Nationality', 'class'=>'col-12 col-sm-3'];
        $fields['c_tribe']=['label'=>'Tribe', 'required'=>true, 'helper'=>'Type Tribe', 'class'=>'col-12 col-sm-3'];
        $fields['c_origin']=['label'=>'Place of Origin', 'required'=>true, 'helper'=>'Type Place of Origin', 'class'=>'col-12 col-sm-2'];
        $fields['c_address']=['label'=>'Home Address', 'required'=>true, 'helper'=>'Type Home Address', 'class'=>'col-12 col-sm-5'];
        $fields['c_phone_number']=['label'=>'Phone Number', 'required'=>true, 'helper'=>'Type Phone Number', 'class'=>'col-12 col-sm-2'];
        $fields['c_admission_date']=['label'=>'Admission Date', 'required'=>true, 'helper'=>'', 'class'=>'col-12 col-sm-3'];
        $fields['c_nok_name']=['label'=>'Next of Kin Name', 'required'=>true, 'helper'=>'Type Next of Kin Name', 'class'=>'col-12 col-sm-4'];
        $fields['c_nok_relationship']=['label'=>'Next of Kin Relationship', 'required'=>true, 'helper'=>'Type Next of Kin Relationship', 'class'=>'col-12 col-sm-4'];
        $fields['c_nok_number']=['label'=>'Next of Kin Phone Number', 'required'=>true, 'helper'=>'Type Next of Kin Phone Number', 'class'=>'col-12 col-sm-4'];
        $fields['c_nok_address']=['label'=>'Next of Kin Address', 'required'=>true, 'helper'=>'Type Next of Kin Address', 'class'=>'col-12 col-sm-4'];
        $fields['c_release_date']=['label'=>'Release Date', 'required'=>false, 'helper'=>'', 'class'=>'col-12 col-sm-4'];
        

        $fields['c_cid']=[
            'label'=>'Case Manager',
            'required'=> false,
            'type'=>'Multiselect',
            'helper'=>'Select Case Manager',
            'class'=>'col-12 col-sm-4',
            'relation'=>[
                'save_table'=>'clients_counselors',
                'parent_field'=>'cc_client_id',
                'child_field'=>'cc_counselor_id',
                'table'=>'counselors',
                'primary_key'=>'counselor_id',
                'display'=>['counselor_name'],
                'order_by'=>'counselor_name',
                'order'=> 'ASC',
                
                    ]
                ];

        $fields['c_image']=[
            'label'=>'Featured Image',
            'type'=>'file',
            'path'=>'./uploads/images',
            'is_image'=>true,
            'max_size'=>'2048',
            'ext_in'=>'png,jpg,gif',
            'wrapper_start'=>'<div class="row"><div class="col-12 col-sm-3 mt-3 mb-3">',
            'wrapper_end'=>'</div></div>',
            'show_file_names'=>true,
            'placeholder'=>'/admin/assets/img/pdf-icon.png',
            'placeholder'=>'/admin/assets/img/file-icon.png',
            'delete_callback'=>'removeFeaturedImage',
            'delete_file'=>true,
            'delete_button_class'=>'btn btn-danger btn-xs',
                ];     
        return $fields;
    }

    public function removeFeaturedImage($parent_id)
	{
		$crud = $this->crud;
		$current_values = $crud->current_values($parent_id);
		if (!$current_values)
			return redirect()->to($crud->getBase() . '/' . $crud->getTable());

		$fileColumnName = 'c_image';
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
}