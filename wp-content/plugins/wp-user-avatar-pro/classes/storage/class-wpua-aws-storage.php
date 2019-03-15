<?php

require_once( dirname( __FILE__ ).'/aws/sdk.class.php' );

class Wpua_Aws_Storage extends Wpua_Storage{

	private $aws_key;

	private $aws_secret;

	private $bucket;

	private $s3;


	public function load() {

	   	$setting = $this->get_storage_option();

	   	if ( empty( $setting['settings']['key'] ) && empty( $setting['settings']['secret_key'] ) ) {
			return new WP_Error( 'apincorrect', __( 'Api key & secret key is required.','wp-user-avatar-pro' ) ); }

		if ( empty( $setting['bucket'] ) ) {
			return new WP_Error( 'bucketnotexists', __( 'Bucket name is required.','wp-user-avatar-pro' ) ); }

	   	$this->aws_key = $setting['settings']['key'];
	   	$this->aws_secret = $setting['settings']['secret_key'];

	   	$this->s3 = new AmazonS3(array(
			            'key' => $this->aws_key,
			            'secret' => $this->aws_secret,
			        ));

		$this->bucket = $setting['bucket'];

		if ( ! $this->s3->if_bucket_exists( $this->bucket )  ) {
			return new WP_Error( 'bucketnotexists', sprintf( 'Bucket %s not available on your aws store.', $this->bucket ) ); }

	}



	public function wpua_avatar_upload( $file ) {

		try {
			 $response = $this->s3->create_object( $this->bucket, $file->name, array(
				 'acl' => 'public-read',
				 'fileUpload' => $file->path,
			 ));
			 $this->avatar_url = $this->s3->get_object_url( $this->bucket, $file->name );
			 $this->avatar_filename = $file->name;
			 $this->resource = $this->bucket;
			 $output = $this->save();
			 if ( is_wp_error( $output ) ) {
				 $this->s3->deleteObject( $this->bucket, $file->name );
				 return $output;
			 }else{
			 	return $output;
			 }			
		} catch ( Exception $e ) {
		    return new WP_Error( 'aws_upload', $e->getMessage() );
		}
	}

}