<?
class Authentications_RESTful_Controller extends RESTful_Controller {
	
	public function index() {
		$this->view()->setView('add');
		$this->add();
	}
	
	public function add() {
		$this->render();
	}
	
	public function create() {
		
		$auth = RESTful_Auth::authenticate( $this->Bookie->db(), $_POST['username'], md5( $_POST['password'] ) );
		
		if ( $auth ) {
			RESTful_Application::run();
			exit;
		} else {
			$this->error = 'Authentication failed';
			$this->view()->setView('add');
			$this->render();
		}
	}
	
	public function destroy() {
		RESTful_Auth::logOff();
		RESTful_Response::redirectTo( '/admin/bookies.html' );
	}
	
	public function before() {
		$this->layout()->setLayout('authenticate');
	}
	
}