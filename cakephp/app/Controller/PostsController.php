<?php

class PostsController extends AppController {
    public $helpers = array('HTML', 'Form');

    public function index() {
        $this->set('posts', $this->Post->find('all'));
    }
}