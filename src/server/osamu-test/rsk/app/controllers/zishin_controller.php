<?php

class ZishinController extends AppController {
	public $name = 'Zishin'; //クラス名
	public $uses = array('AnswerHistory','Badge','BadgeAcquirements','City','Establish','User','Prefecture','Question');
	//public $autoRender = true;
	var $components = array('RequestHandler');
	public $layout = null;
	
    function index(){
        $recipes = $this->User->find('all');
        $this->set(compact('recipes'));
    }
	//制覇API
    function establish($id){
        $recipe = $this->Establish->findById($id);
        $this->set(compact('recipe'));
    }
	//バッジAPI (マスタ)
	function badge($id){
        $recipe = $this->Badge->findById($id);
        $this->set(compact('recipe'));
		$this->render('establish');//制覇APIと同じビュー
    }
	//問題API (マスタ)
	function question($id){
        $recipe = $this->Question->findById($id);
        $this->set(compact('recipe'));
		$this->render('establish');//制覇APIと同じビュー
    }

	function add($name){
		$this->TsVenue->name = $name;
		if ($this->TsVenue->save($this->data)){
            $message = 'Saved';
        } else {
            $message = 'Error';
        }
		$this->set(compact("message"));
	}

    function edit($id) {
        $this->Recipe->id = $id;
        if ($this->Recipe->save($this->data)) {
            $message = 'Saved';
        } else {
            $message = 'Error';
        }
        $this->set(compact("message"));
    }
	
    function delete($id) {
        if($this->TsVenue->delete($id)) {
            $message = 'Deleted';
        } else {
            $message = 'Error';
        }
        $this->set(compact("message"));
    }
	
	
}
?>