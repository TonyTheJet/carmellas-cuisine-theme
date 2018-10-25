<?php
	class HomePopUp{
		
		//constants
		const END_DATE_META_KEY = 'end_date';
		const POPUP_PAGE_ID = 222;
		const START_DATE_META_KEY = 'start_date';
		const STATUS_PUBLISH = 'publish';
		
		//properties
		private $end_date = null;
		private $post = null;
		private $start_date = null;
		
		//methods
		function __construct(){
			$this->post = get_post(self::POPUP_PAGE_ID);
			
			$this->end_date = get_post_meta($this->post->ID, self::END_DATE_META_KEY, true);
			$this->start_date = get_post_meta($this->post->ID, self::START_DATE_META_KEY, true);
		}
		
		public function get_end_date(){ return $this->end_date; }
		
		/**
		* returns the WP_Post object associated with this wrapper class
		* 
		* @return WP_Post
		*/
		public function get_post(){ return $this->post; }
		
		public function get_start_date(){ return $this->start_date; }
		
		/**
		* determines whether the post is active
		* 
		* @return bool
		*/
		public function is_active(){
			if (
				!empty($this->post) 
				&& is_a($this->post, 'WP_Post') 
				&& $this->post->post_status == self::STATUS_PUBLISH
				&& time() >= strtotime($this->start_date)
				&& time() <= strtotime($this->end_date)
			):
				return true;
			else:
				return false;
			endif;
		}
	}