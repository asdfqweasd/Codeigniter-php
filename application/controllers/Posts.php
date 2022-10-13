<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Posts extends CI_Controller{
    public function __contstruct()
    {
        parent:: __contstruct(); //parent class of construct
        $this->load->library('form_validation');
        $this->load->model("post_model");
        $this->load->model('Main_model');
        $this->load->helper('url');
         // Userid 
        $this->session->set_userdata(array("userid"=>3));
       
    }
    public function index(){
        
        // switch first char to Uppercase
        if(!$this->session->userdata('logged_in'))
        {
            redirect('login');
        }
        $data['title'] = 'Latest Posts';

        $data['posts'] = $this -> Post_model -> get_posts();
        

        $this->load->view('templates/header');
        $this->load->view('posts/index', $data);
        $this->load->view('templates/footer');
        
        
        
        // $data['rating'] = $this -> Main_model -> get_rating($id);
        // var_dump($data['rating']);
        // Userid
        $userid = $this->session->userdata('userid');

        
        // Fetch local post records
        // $data['posts'] = $this->Main_model->getAllPosts($userid);
    
        
    }

    public function view($slug = NULL){
        $data['post'] = $this ->Post_model -> get_posts($slug);
        if(empty($data['post'])){
            show_404( );
        }
        $data['ratings'] = $this-> Post_model -> get_rating($data['post']['id']);

        $data['Avgratings'] = $this-> Post_model -> get_AvgRating($data['post']['id']);


        $data['title'] = $data['post']['title'];
        $this->load->view('templates/header');
        $this->load->view('posts/view', $data);
        $this->load->view('templates/footer');
    }

    public function create(){
        if(!$this->session->userdata('logged_in'))
        {
            redirect('login');
        }
        $data['title'] = "Create Post";
        $this->load->library('form_validation');
        $this -> form_validation -> set_rules('title', 'Title','required');

        $this -> form_validation -> set_rules('body', 'Body','required');

        // flase means validation doesn't run else means form has been submiited
        if($this-> form_validation->run() === FALSE){
            $this->load->view('templates/header');
            $this->load->view('posts/create', $data);
            $this->load->view('templates/footer');
        }else{
            $this -> Post_model ->create_post();
            redirect('posts');
        }

    }

    public function delete($id){
            $this -> Post_model -> delete_post($id);
            redirect('posts');
    }

    public function edit($slug){
        $data['post'] = $this ->Post_model -> get_posts($slug);
        if(empty($data['post'])){
            show_404( );
        }

        $data['title'] = 'Edit Post';
        $this->load->view('templates/header');
        $this->load->view('posts/edit', $data);
        $this->load->view('templates/footer');
    }

    public function update(){
        $this->Post_model->update_post();
        redirect('posts');
    }

     // Update rating
    public function updateRating(){


    // POST values
    $postid = $this->input->post('postid');
    $rating = $this->input->post('rating');

    // Update user rating and get Average rating of a post
    $averageRating = $this->Post_model->userRating($postid,$rating);

    echo $averageRating;
    exit;
     }


}

?>