<?php

namespace VAJOFOWPPGNext\Admin\Utilities;

class ImageUploader {

    private $sourceImagePath;
    private $postId;
    private $fileName;
    private $fileType;
    
    /**
     * Constructor to initialize the uploader
     * 
     * @param string $sourceImagePath Path to the source image
     * @param int $postId WordPress post ID
     */
    public function __construct($sourceImagePath, $postId) {

        $this->sourceImagePath = $sourceImagePath;
        $this->postId = $postId;
        $this->fileName = basename($sourceImagePath);
        $this->fileType = wp_check_filetype($this->fileName)['type'];
    }
    
    /**
     * Upload image to WordPress media library
     * 
     * @return int|WP_Error Attachment ID if successful, WP_Error on failure
     */
    public function uploadToMediaLibrary() {
        
        // Check if file exists
        if (!file_exists($this->sourceImagePath)) {
            return new \WP_Error('file_not_found', 'Source image file not found');
        }
        
        // Get WordPress upload directory
        $uploadDir = wp_upload_dir();
        
        // Generate unique filename
        $uniqueFileName = wp_unique_filename($uploadDir['path'], $this->fileName);
        $uploadPath = $uploadDir['path'] . '/' . $uniqueFileName;
        
        // Copy file to uploads directory
        if (!copy($this->sourceImagePath, $uploadPath)) {
            return new \WP_Error('upload_failed', 'Failed to copy image to uploads directory');
        }
        
        // Prepare attachment data
        $attachment = array(
            'guid'           => $uploadDir['url'] . '/' . $uniqueFileName,
            'post_mime_type' => $this->fileType,
            'post_title'     => preg_replace('/\.[^.]+$/', '', $uniqueFileName),
            'post_content'   => '',
            'post_status'    => 'inherit'
        );
        
        // Insert attachment into media library
        $attachmentId = wp_insert_attachment($attachment, $uploadPath, $this->postId);
        
        if (is_wp_error($attachmentId)) {
            return $attachmentId;
        }
        
        // Generate attachment metadata
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        $attachmentData = wp_generate_attachment_metadata($attachmentId, $uploadPath);
        wp_update_attachment_metadata($attachmentId, $attachmentData);
        
        return $attachmentId;
    }
    
    /**
     * Set image as featured image for the post
     * 
     * @param int $attachmentId Attachment ID
     * @return bool True on success, false on failure
     */
    public function setAsFeaturedImage($attachmentId) {
        return set_post_thumbnail($this->postId, $attachmentId);
    }
    
    /**
     * Upload and set as featured image in one go
     * 
     * @return int|WP_Error Attachment ID if successful, WP_Error on failure
     */
    public function uploadAndSetAsFeatured() {
        $attachmentId = $this->uploadToMediaLibrary();
        
        if (is_wp_error($attachmentId)) {
            return $attachmentId;
        }
        
        $success = $this->setAsFeaturedImage($attachmentId);
        
        if (!$success) {
            return new \WP_Error('featured_image_failed', 'Failed to set featured image');
        }
        
        return $attachmentId;
    }
}