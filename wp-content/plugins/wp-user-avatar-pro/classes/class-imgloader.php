<?php
class CI_Image_Uploader {
	private $new_width = 150;
	private $new_height = 150;
	private $crop_x = 0;
	private $crop_y = 0;
	private $crop_w = 0; // Width of cropped file
	private $crop_h = 0; // Height of cropped file
	private $jpeg_quality = 90;
	public $folder = 'upload';
	private $response = array();
	public $file_name = '';
	public  $mkdir_mode = 0755;
	function get_response() {
		return $this->response;
	}
	function __construct() {
		global $_FILES,$_POST;

		$this->file = $_POST['ci_blob'];

		if ( ! isset( $_POST['file_type'] ) ) {
			$this->response['error'] = 'Invalid File Type or size.';
		}

		$this->file_type = $_POST['file_type'];

		if ( isset( $_POST['ci_crop_x'] ) and $_POST['ci_crop_x'] != '' ) {
			$this->crop_x = $_POST['ci_crop_x']; }

		if ( isset( $_POST['ci_crop_y'] ) and $_POST['ci_crop_y'] != '' ) {
			$this->crop_y = $_POST['ci_crop_y']; }

		if ( isset( $_POST['ci_crop_w'] ) and $_POST['ci_crop_w'] != '' ) {
			$this->crop_w = $_POST['ci_crop_w']; }

		if ( isset( $_POST['ci_crop_h'] ) and $_POST['ci_crop_h'] != '' ) {
			$this->crop_h = $_POST['ci_crop_h']; }
	}


	function validate() {

		$validextensions = array( 'jpeg', 'gif', 'png' );

		if ( in_array( $this->file_type, $validextensions ) ) {

			return true;

		} else {

			$this->response['error'] = 'Invalid File Format or Size.';
			return false;
		}

	} // Validation Ended;



	function save_image($width = 150,$height = 150) {

		if ( $this->validate() ) {

			if ( ! is_dir( $this->folder ) ) {
				mkdir( $this->folder, $this->mkdir_mode, true );
			}
			$blob_data = str_replace( 'data:image/'.$this->file_type.';base64,','',$_POST['ci_blob'] );
			$temp_file = $this->folder.'/temp'.time().'.'.$this->file_type;
			$myfile = fopen( $temp_file,'w' );
			fwrite( $myfile, base64_decode( $blob_data ) );
			fclose( $myfile );

			$source_path = $temp_file;
			list($source_width,$source_height) = getimagesize( $source_path );

			if ( $this->crop_w == 0 ) {
				$this->crop_w = $source_width; }

			if ( $this->crop_h == 0 ) {
				$this->crop_h = $source_height; }

			if ( isset( $this->file_name ) and $this->file_name != '' ) {
				$filename_a = $this->file_name.'a.'.$this->file_type;
				$filename_b = $this->file_name.'b.'.$this->file_type;

				if ( file_exists( $this->folder.'/'.$filename_a ) ) {
					@unlink( $this->folder.'/'.$filename_a );
					$filename = $filename_b;
				} else if ( file_exists( $this->folder.'/'.$filename_b ) ) {
					@unlink( $this->folder.'/'.$filename_b );
					$filename = $filename_a;

				} else {
					$filename = $filename_a;
				}
			} else { 	$filename = 'avatar'.time().'.'.$this->file_type; }

			$target_path = $this->folder.'/'.$filename;

			if ( $this->file_type == 'jpeg' ) {
				$img_r = imagecreatefromjpeg( $source_path );
			} else if ( $this->file_type == 'png' ) {
				$img_r = imagecreatefrompng( $source_path );
			} else if ( $this->file_type == 'gif' ) {
				$img_r = imagecreatefromgif( $source_path );
			}

			if($width == '' or intval($width) <=0) {
				$width = $source_width;
			}

			if($height == '' or intval($height) <=0) {
				$height = $source_height;
			}


			$dst_r = ImageCreateTrueColor( $width, $height );

		if( $this->file_type == 'png') {
				imagesavealpha( $dst_r, true );
				$color = imagecolorallocatealpha( $dst_r, 0, 0, 0, 127 );
				imagefill( $dst_r, 0, 0, $color );
			}
			imagecopyresampled($dst_r,$img_r,0,0,$this->crop_x,$this->crop_y,
			$width, $height,$this->crop_w,$this->crop_h);

			if ( $this->file_type == 'jpeg' ) {
				   imagejpeg( $dst_r,$target_path,90 );

			} else if ( $this->file_type == 'png' ) {
				   imagepng( $dst_r,$target_path,9 );

			} else if ( $this->file_type == 'gif' ) {
				imagegif( $dst_r,$target_path,90 );

			}

			if ( file_exists( $target_path ) ) {
				$stat = stat( dirname( $target_path ) );
				$perms = $stat['mode'] & 0000666;
				@chmod( $target_path, $perms );

			}
			unlink( $temp_file );

			$this->response['img'] = $this->folder_url.$filename;
			$this->response['filename'] = $filename;
			return true;
		} else {
			return false;
		}

	}

}
