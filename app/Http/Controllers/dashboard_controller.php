<?php

namespace App\Http\Controllers;

use App\dashboard_model;
use App\Models;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Company;


class dashboard_controller extends Controller {
  
  public function __construct() {
    $this->middleware('auth');
  }
   
   public function index() {

    $this->agregarDatosASession();


       $data = [
           'name' =>  'GUMA@NET'
       ];
      
       return view('pages.dashboard',$data);
   }

   public function agregarDatosASession(){
    $request = Request();
    $ApplicationVersion = new \git_version();
     $company = Company::where('id',$request->session()->get('company_id'))->first();// obtener nombre de empresa mediante el id de empresa
     $request->session()->put('ApplicationVersion', $ApplicationVersion::get());
     $request->session()->put('companyName', $company->nombre);// agregar nombre de compañia a session[], para obtenert el nombre al cargar otras pagina 
   }

	public function getDetalleVentas($tipo) {
		$obj = dashboard_model::getDetalleVentas($tipo);
		return response()->json($obj);
	}
}
