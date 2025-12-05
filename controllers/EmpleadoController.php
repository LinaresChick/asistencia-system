<?php
// controllers/EmpleadoController.php
class EmpleadoController extends Controller {
    private $model;
    public function __construct(){
        $this->model = new EmpleadoModel();
    }

    public function ficha(){
        $dni = $_GET['dni'] ?? null;
        if(!$dni){
            $this->json(['success'=>false, 'message'=>'DNI requerido']);
        }
        $emp = $this->model->findByDNI($dni);
        if(!$emp) $this->json(['success'=>false, 'message'=>'No encontrado']);
        $this->json(['success'=>true, 'data'=>$emp]);
    }
}
