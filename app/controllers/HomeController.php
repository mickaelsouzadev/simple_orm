<?php  

namespace App\Controllers;
use App\Models\User;

class HomeController extends Controller{
    
    public function __construct()
    {
        parent::__construct();
        // $this->model->setTable("Review");
        $this->model = new User();
    }
    
    public function index()
    {
        $db_data = $this->model->select()->returnArray();

        echo "<br><pre>";
        print_r($db_data);
        echo "</pre>";

		$data['title'] = "Welcome!";
        $this->view->loadPage("home",$data);
    }
    
}