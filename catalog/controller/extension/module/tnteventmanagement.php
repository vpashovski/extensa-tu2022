<?php
class ControllerExtensionModuletnteventmanagement extends Controller {

	public function index() {
		$data = array();
		return $this->load->view('extension/module/tnteventmanagement', $data);

	}
	public function ajax() {
		$calendarData = array();

		$this->load->model('tnt/tntallquery');
		$setting['status'] = 1;
		if(isset($setting['status'])){
			$eventlist 			= $this->model_tnt_tntallquery->geteventlist();
			$data['eventlist'] 	= array(); 		
			foreach ($eventlist as $key => $value) {
				if(isset($value['tnteventmanagement_status'])){
					$d = new DateTime($value['tnteventmanagementparent_start_date']);
					$start = $d->getTimestamp() * 1000;

					$d = new DateTime($value['tnteventmanagementparent_end_date']);
					$end = $d->getTimestamp() * 1000;
					// $start = $value['tnteventmanagementparent_start_date'];
					// $end = $value['tnteventmanagementparent_end_date'];	

					$calendar[] = array(
						'id'			=> $value['tnteventmanagementparent_id'],
						'start'			=> $start,
						'url'			=> '#',
						'end'			=> $end,
						'title'			=> $value['tnteventmanagementchild_title'],
						'description'	=> $value['tnteventmanagementchild_description']
					);
				}
			}
			$calendarData = array(
				"success" => 1,	
			    "result"=>$calendar
			);
			echo json_encode($calendarData);
			exit;		
		}
	}
}


