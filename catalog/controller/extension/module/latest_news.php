<?php
class ControllerExtensionModuleLatestNews extends Controller {
	public function index() {
		$this->load->language('extension/module/latest_news');

		$this->load->model('setting/setting');

        $settings = $this->model_setting_setting->getSetting('module_latest_news');

		$data['news'] = array();

        $this->load->model('extension/module/latest_news');

        $fliter = array();

        if (isset($settings['module_latest_news_limit'])) {
            $fliter['limit'] = $settings['module_latest_news_limit'];
        }

        $infoPages = $this->model_extension_module_latest_news->getInformations($fliter);

        foreach ($infoPages as $infoPage) {
			$data['news'][] = array(
				'title' => $infoPage['title'],
				'title2' => $infoPage['title'],
				'href'  => $this->url->link('information/information', 'information_id=' . $infoPage['information_id'])
			);
		}

		return $this->load->view('extension/module/latest_news', $data);
	}
}