<?php

require_once "classes/tools.php";

class Media {
    public $id;
    public $path;
    private $file;

    function __construct($file = null) {
        # Just creates a image
        $this->file = $file;
    }

    function saveImage() {
        # Save's the image and returns a status
        $imageFileType = strtolower(pathinfo($this->file['name'], PATHINFO_EXTENSION));
        $this->id = Tools::generateRandomString(12);

        $check = getimagesize($this->file["tmp_name"]);
        if($check == false) {
            return 'invalidFileFormat';
        }

        $check = getimagesize($this->file["tmp_name"]);
        if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) {
            return 'invalidImageFormat';
        } 

        $this->path = $this->id .  '.' . $imageFileType;
        $targetFile = $_SERVER['DOCUMENT_ROOT'] . '/media/' . $this->path;
        move_uploaded_file($this->file["tmp_name"], $targetFile);
        return true;
    }
}