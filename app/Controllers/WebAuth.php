<?php namespace App\Controllers;

class WebAuth extends BaseController
{
	public function index()
	{
        $data['title'] = 'Login Page';
        $data['form_open'] = form_open('webauth/do_auth');
        $data['form_submit'] = form_submit('mysubmit', 'Login', ['class' => 'btn btn-primary btn-user btn-block']);
        $data['form_close'] = form_close();

        $data['sess_error'] = session()->getFlashdata('error');

        echo $this->renderPage('management/ManagementAuthView', $data);
    }

    public function do_auth() {
        $post = $this->request;

        $mgmtAuthModel = new \App\Models\MgmtAuthModel($this->db);

        $username = $post->getPost("mgmtUsername");
        $password = $post->getPost("mgmtPassword");

        $result = $mgmtAuthModel->auth($username, $password);

        if($result['status'] == 'ok') {
            session()->set($result['data']);
        } else {
            session()->setFlashdata('error', $result['msg']);
        }

        return redirect("webauth");
    }
}