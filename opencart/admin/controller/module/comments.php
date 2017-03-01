<?php
class ControllerModuleComments extends Controller {
    private $error = array(); // объявляем переменную - массив с возможными ошибками
    public function index() {
        $this->load->language('module/comments');
        $this->document->setTitle($this->language->get('heading_title'));
        $this->load->model('setting/setting');
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->model_setting_setting->editSetting('comments', $this->request->post);
            $this->session->data['success'] = $this->language->get('text_success');
            $data['success'] = $this->session->data['success'] ;
        }

        $data['heading_title'] = $this->language->get('heading_title');
        $data['text_edit'] = $this->language->get('text_edit');
        $data['text_enabled'] = $this->language->get('text_enabled');
        $data['text_disabled'] = $this->language->get('text_disabled');
        $data['table_image'] = $this->language->get('table_image');
        $data['table_name'] = $this->language->get('table_name');
        $data['table_text'] = $this->language->get('table_text');
        $data['button_save'] = $this->language->get('button_save');
        $data['button_cancel'] = $this->language->get('button_cancel');
        $data['table_header'] = $this->language->get('table_header');
        $data['table_edit'] = $this->language->get('table_edit');
        $data['table_delete'] = $this->language->get('table_delete');
        $data['table_id'] = $this->language->get('table_id');
        $data['title_add'] = $this->language->get('title_add');
        $data['token'] = $this->session->data['token'];

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        $data['breadcrumbs'] = array();
        // Добавляем по одной крошки, сначала ссылка на главную страницу
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'), // text_home по всей видимости доступен отовсюду
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
        );
        // добавляем ссылку на список с модулями, прописано в своем языковом файле
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_module'),
            'href' => $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL')
        );

        $data['action'] = $this->url->link('module/comments', 'token=' . $this->session->data['token'], 'SSL');
        $data['cancel'] = $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL');

        $data['comments'] = array();
        $this->load->model('comments/comments');
        $data['comments'] = $this->model_comments_comments->getAll();


        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('module/comments.tpl', $data));

    }

    public function addComment(){
        // получаем значения
        $name = ($this->request->post['name']) ? $this->request->post['name']:'default';
        $image = ($this->request->post['image']) ? $this->request->post['image']:1;
        $text = ($this->request->post['text']) ? $this->request->post['text']:'no comment';
        $id = (isset($this->request->post['id'])) ? $this->request->post['id']:0;
        // массив вывода
        $json = array();
        $json['name']=$name;
        $json['image']=$image;
        $json['text']=$text;
        $this->load->model('comments/comments');
        $json['com_id'] = ($id) ? $this->model_comments_comments->updateRow($id,$name,$image,$text) : $this->model_comments_comments->addNew($name,$image,$text);
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    protected function validate() {
        if (!$this->user->hasPermission('modify', 'module/comments')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }
        return !$this->error;
    }

    public function deleteComment(){
        // получаем значения
        $text = ($this->request->post['id']) ? $this->request->post['id'] : 0;
        // массив вывода
        $json = array();
        if (!$text) {
            $json['error'] = 'no id';
        } else {
            $this->load->model('comments/comments');
            $json['com_id'] = $this->model_comments_comments->deleteRow($text);
        }
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }






}