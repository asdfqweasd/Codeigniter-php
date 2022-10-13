<?php
    class Post_model extends CI_Model{
        public function __construct(){
            $this->load->database();
        }

        // set slug Flase at default
        public function get_posts($slug = FALSE){
            if($slug === FALSE){
                $this->db->order_by('id','DESC');
                $query = $this -> db -> get('posts');
                return $query -> result_array();
            }
            // get slugs put in array get where is where sql 
            $query = $this -> db -> get_where('posts', array('slug' => $slug));
            return $query -> row_array();
        }

        public function create_post(){
            $slug = url_title($this->input->post('title'));

            $data = array(
                'title' => $this->input ->post('title'),
                'slug' => $slug,
                'body' => $this ->input->post('body')
            );

            return $this-> db->insert('posts',$data);
        }

        public function delete_post($id){
            $this->db->where('id',$id);
            $this->db->delete('posts');
            return true;
        }

        public function update_post(){
            $slug = url_title($this->input->post('title'));
            $data = array(
                'title' => $this->input ->post('title'),
                'slug' => $slug,
                'body' => $this ->input->post('body')
            );
            $this->db->where('id',$this->input->post('id'));
            return $this-> db->update('posts',$data);
        }

        public function get_rating($id) {
            $posts_arr = array();
            
            // User rating
            $this->db->select('rating');
            $this->db->from('posts_rating');
            // $this->db->where("userid", $userid);
            $this->db->where("postid", $id);
            $userRatingquery = $this->db->get();
      
            $userpostResult = $userRatingquery->result_array();
      
            $userRating = 0;
            if(count($userpostResult)>0){
               $userRating = $userpostResult[0]['rating'];
            }
            return $userRating;
      
        }

        public function get_AvgRating($id) {
         // Average rating
         $this->db->select('ROUND(AVG(rating),1) as averageRating');
         $this->db->from('posts_rating');
         $this->db->where("postid", $id);
         $ratingquery = $this->db->get();
   
         $postResult = $ratingquery->result_array();
   
         $Avgrating = $postResult[0]['averageRating'];
   
         if($Avgrating == ''){
            $Avgrating = 0;
         }

         return $Avgrating;
        }

        public function userRating($postid,$rating){
            $this->db->select('*');
            $this->db->from('posts_rating');
            $this->db->where("postid", $postid);
            $userRatingquery = $this->db->get();
        
            $userRatingResult = $userRatingquery->result_array();
            if(count($userRatingResult) > 0){
        
              $postRating_id = $userRatingResult[0]['id'];
              // Update
              $value=array('rating'=>$rating);
              $this->db->where('id',$postRating_id);
              $this->db->update('posts_rating',$value);
            }else{
              $userRating = array(
                "postid" => $postid,
                "rating" => $rating
              );
        
              $this->db->insert('posts_rating', $userRating);
            }
        
            // Average rating
            $this->db->select('ROUND(AVG(rating),1) as averageRating');
            $this->db->from('posts_rating');
            $this->db->where("postid", $postid);
            $ratingquery = $this->db->get();

            $postResult = $ratingquery->result_array();

            $rating = $postResult[0]['averageRating'];

            if($rating == ''){
            $rating = 0;
            }

            return $rating;
        }

    }

    ?>