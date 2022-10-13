<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Upload extends CI_Controller
{
	
    public function index()
    {
		$this->load->model('File_model');
		$data = array(); 

        // Get files data from the database 
        $data['files'] = $this->file->getRows(); 
         
        // Pass the files data to view 
        $this->load->view('pages/file', $data); 

		$this->load->view('templates/header'); 
    	if (!$this->session->userdata('logged_in'))//check if user already login
		{	
			if (get_cookie('remember')) { // check if user activate the "remember me" feature  
				$username = get_cookie('username'); //get the username from cookie
				$password = get_cookie('password'); //get the username from cookie
				if ( $this->user_model->check_login($username, $password) )//check username and password correct
				{
					$user_data = array('username' => $username,'logged_in' => true );
					$this->session->set_userdata($user_data); //set user status to login in session
		 			$this->load->view('pages/file',array('error' => ' ')); //if user already logined show upload page
				}
			}else{
				redirect('login'); //if user already logined direct user to home page
			}
		}else{
			$this->load->view('pages/file',array('error' => ' ')); //if user already logined show login page
		}
		$this->load->view('templates/footer');
    }

	public function do_upload()
	{
		$this->load->model('File_model');
		$data = array(); 
		// $errorUploadType = $statusMsg = '';
		 // If file upload form submitted 
		 if($this->input->post('fileSubmit')){ 
             
            // If files are selected to upload 
            if(!empty($_FILES['files']['name']) && count(array_filter($_FILES['files']['name'])) > 0){ 
                $filesCount = count($_FILES['files']['name']); 
                for($i = 0; $i < $filesCount; $i++){ 
                    $_FILES['file']['name']     = $_FILES['files']['name'][$i]; 
                    $_FILES['file']['type']     = $_FILES['files']['type'][$i]; 
                    $_FILES['file']['tmp_name'] = $_FILES['files']['tmp_name'][$i]; 
                    $_FILES['file']['error']     = $_FILES['files']['error'][$i]; 
                    $_FILES['file']['size']     = $_FILES['files']['size'][$i]; 
                     
                    // File upload configuration 
                    $config['upload_path'] = './uploads/';  
                    $config['allowed_types'] = '*'; 
                    //$config['max_size']    = '100'; 
                    //$config['max_width'] = '1024'; 
                    //$config['max_height'] = '768'; 
                     
                    // Load and initialize upload library 
                    $this->load->library('upload', $config); 
                    $this->upload->initialize($config); 
                     
                    // Upload file to server 
                    if($this->upload->do_upload('file')){ 
                        // Uploaded file data 
						$this->File_model->upload($this->upload->data('file_name'), $this->upload->data('full_path'),$this->session->userdata('user_email'));
                    }else{  
                        $errorUploadType .= $_FILES['file']['name'].' | ';  
                    } 
                } 
				$this->session->set_flashdata("success_msg","Upload successful");
				$this->load->view('templates/header');
				$this->load->view('pages/file');
				$this->load->view('templates/footer');
                 
                // $errorUploadType = !empty($errorUploadType)?'<br/>File Type Error: '.trim($errorUploadType, ' | '):''; 
                if(!empty($uploadData)){ 
                    // Insert files data into the database 
					$this->File_model->upload($this->upload->data('file_name'), $this->upload->data('full_path'),$this->session->userdata('user_email'));
					$this->load->view('templates/header');
					$this->load->view('pages/file');
					$this->load->view('templates/footer');
                    // Upload status message 
                    // $statusMsg = $insert?'Files uploaded successfully!'.$errorUploadType:'Some problem occurred, please try again.'; 
                // }else{ 
                //     $statusMsg = "Sorry, there was an error uploading your file.".$errorUploadType; 
                } 
            }else{ 
                // $statusMsg = 'Please select image files to upload.'; 
				$this->session->set_flashdata("error_msg","Upload failure");
				$this->load->view('templates/header');
				$this->load->view('pages/file');
				$this->load->view('templates/footer');
            } 
        } 
         
        
	}



	function dragDropUpload(){ 
		$this->load->model('File_model');
        if(!empty($_FILES)){ 
            // File upload configuration 
            $config['upload_path'] = './uploads/'; 
            $config['allowed_types'] = '*'; 
             
            // Load and initialize upload library 
            $this->load->library('upload', $config); 
            $this->upload->initialize($config); 
             
            // Upload file to the server 
            if($this->upload->do_upload('file')){ 
				$this->File_model->upload($this->upload->data('file_name'), $this->upload->data('full_path'),$this->session->userdata('user_email'));
            } 
        } 
    }
    
    
    function thumbnail(){ 
        $this->uploadPath = './uploads/images/'; 
        $thumb_msg = $status = $status_msg = $thumbnail = $org_image_size = $thumb_image_size = ''; 
        $data = array(); 
 
        // If the file upload form submitted 
        if($this->input->post('submit')){ 
            if(!empty($_FILES['image']['name'])){ 
                // File upload config 
                $config['upload_path']   = $this->uploadPath; 
                $config['allowed_types'] = 'jpg|jpeg|png'; 
                 
                // Load and initialize upload library 
                $this->load->library('upload', $config); 
                 
                // Upload file to server 
                if($this->upload->do_upload('image')){ 
                    $uploadData = $this->upload->data(); 
                    $uploadedImage = $uploadData['file_name']; 
                    $org_image_size = $uploadData['image_width'].'x'.$uploadData['image_height']; 
                     
                    $source_path = $this->uploadPath.$uploadedImage; 
                    $thumb_path = $this->uploadPath.'thumb/'; 
                    $thumb_width = 280; 
                    $thumb_height = 175; 
                     
                    // Image resize config 
                    $config['image_library']    = 'gd2'; 
                    $config['source_image']     = $source_path; 
                    $config['new_image']         = $thumb_path; 
                    $config['maintain_ratio']     = FALSE; 
                    $config['width']            = $thumb_width; 
                    $config['height']           = $thumb_height; 
                     
                    // Load and initialize image_lib library 
                    $this->load->library('image_lib', $config); 
                     
                    // Resize image and create thumbnail 
                    if($this->image_lib->resize()){ 
                        $thumbnail = $thumb_path.$uploadedImage; 
                        $thumb_image_size = $thumb_width.'x'.$thumb_height; 
                        $thumb_msg = '<br/>Thumbnail created!'; 
                    }else{ 
                        $thumb_msg = '<br/>'.$this->image_lib->display_errors(); 
                    } 
                     
                    $status = 'success'; 
                    $status_msg = 'Image has been uploaded successfully.'.$thumb_msg; 
                }else{ 
                    $status = 'error'; 
                    $status_msg = 'The image upload has failed!<br/>'.$this->upload->display_errors('',''); 
                } 
            }else{ 
                $status = 'error'; 
                $status_msg = 'Please select a image file to upload.';  
            } 
        } 
         
        // File upload status 
        $data['status'] = $status; 
        $data['status_msg'] = $status_msg; 
        $data['thumbnail'] = $thumbnail; 
        $data['org_image_size'] = $org_image_size; 
        $data['thumb_image_size'] = $thumb_image_size; 
         
        // Load form view and pass upload status 
        $this->load->view('templates/header');
        $this->load->view('pages/file', $data); 
        $this->load->view('templates/footer');
    } 
}

