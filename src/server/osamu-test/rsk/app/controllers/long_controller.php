<?php 

class LongController extends AppController {
	public $name = 'Long'; //クラス名
	public $uses = array('User'); //使用するモデル
	//public $uses = null; //使用するモデル
	//public $autoRender = true;
	public $layout = null;
	
	function index(){
		$this->set('hoge',"hoge");
		debug($this->User->find('all'));
	}
}
?>