<?php
// controllers/AppController.php
class AppController extends Controller {
    public function about(){
        $this->view('app/about', []);
    }
}
?>
