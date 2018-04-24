<?php

/** 
 * CRM system for 9daye
 * 
 * @author Vett <niulechuan@9daye.com.cn>
 */
namespace common\components\upload;

use common\exceptions;

class FileManager
{
    private $path;

    private $fileInfo;

    private $names;

    public function __construct($path)
    {
        if(realpath($path) && !empty($path)) {
            $this->path = $path;
        } else {
            $this->path = $this->defaultPath();
        }

        if(empty($_FILES)) {
            throw new exceptions\RuntimeException('Empty upload files.');
        }

        $this->fileInfo = $_FILES;

        $names = [];
        foreach($this->fileInfo as $key => $v) {
            $names[] = $key;
        }
        $this->names = $names;

        $this->checkError();
    }

    public function checkError()
    {
        $file = $this->fileInfo;
        foreach($this->names as $name) {
            if(! is_array($file[$name]['error'])) {
                throw new exceptions\RuntimeException(
                    'Component Upload only support array params. Please use array to POST data.'
                );
            }
            foreach($file[$name]['error'] as $error) {
                switch ($error) {
                case UPLOAD_ERR_OK:
                    break;
                case UPLOAD_ERR_NO_FILE:
                    throw new exceptions\RuntimeException('No file sent.');
                case UPLOAD_ERR_INI_SIZE:
                case UPLOAD_ERR_FORM_SIZE:
                    throw new exceptions\RuntimeException('Exceeded filesize limit.');
                default:
                    throw new exceptions\RuntimeException('Unknown errors.');
                }
            }
        }
    }

    public function getInfo()
    {
        $moved = [];
        $file = $this->fileInfo;
        foreach($this->names as $name) {
            foreach($file[$name]['name'] as $key => $value) {
                $realName = $this->path . '/' . $file[$name]['name'][$key];
                $moved[$name][$key]['name'] = $this->move($file[$name]['tmp_name'][$key], $realName);
                $moved[$name][$key]['size'] = $file[$name]['size'][$key];

            }
        }
        return $moved;
    }

    private function move($tmpName, $name)
    {
        $name = mb_convert_encoding($name, 'UTF-8');
       
        if(move_uploaded_file($tmpName, $name)) {
            return $name;
        }
        return false;
    }

    private function defaultPath()
    {
        return realpath(__DIR__ . '/../../../upload/');
    }
}
