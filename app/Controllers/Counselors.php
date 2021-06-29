<?php
namespace App\Controllers;
use App\Libraries\Crud;
class Tags extends BaseController{
    protected $crud;

    function __construct(){
        $params =[
            'table'=>'counselors',
            'dev'=>'false',
            'fields'=>$this->field_options(),
            'form_title_add'=>'Add Counselor',
            'form_title_update'=> 'Edit Counselor',
            'form_submit'=>'Add',
            'table_title'=>'Counselors',
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
        $columns = ['t_id', 't_name'];
        $where = null; //['u_status ='=>'Active'];
        $order = [
            ['t_name', 'ASC']
        ];
        $data['table']=$this->crud->view($page, $per_page, $columns, $where, $order);
        return view('admin/counselors/table', $data);
    }
    public function add(){
        $data['form'] = $form = $this->crud->form();
        $data['title'] = $this->crud->getAddTitle();

        if(is_array($form) && isset($form['redirect']))
            return redirect()->to($form['redirect']);
        return view('admin/tags/form', $data);
        }

    public function edit($id){
        if(!$this->crud->current_values($id)){
            return redirect()->to($this->crud->getBase() . '/' . $this->crud->getTable());
        }

        $data['form'] = $form = $this->crud->form();
        $data['title'] = $this->crud->getEditTitle();

        if(is_array($form) && isset($form['redirect']))
            return redirect()->to($form['redirect']);
        return view('admin/counselors/form', $data);
        }
    
    protected function field_options(){
        $fields = [];

        $fields['t_id']=['label'=>'ID'];
        $fields['t_name']=['label'=>'Name', 'required'=>true];
        
        return $fields;
    }
}