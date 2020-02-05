<?php

namespace Frontend\Controllers;

use Phalcon\Mvc\Controller;

class IndexController extends Controller
{
    public function indexAction()
    {
        if ($this->request->isPost()) {
            $link = $this->request->getPost('url');
            $requestId = uniqid();

            $result = $this
                ->getDI()
                ->get('url-short-jrpc')
                ->call('short-link.create', ['link' => $link], $requestId);

            $data = json_decode($result);

            if (is_null($data)) {
                $this->view->setParamToView('error', 'Api request error');
                return $this->view;
            }

            if ($data->id === $requestId && $data->result) {
                $this->view->setParamToView('shortlink', 'http://domain.com/' . $data->result);
            } else {
                $this->view->setParamToView('error', $data->error->data ?: $data->error->message);
            }
        }

        return $this->view;
    }
}
