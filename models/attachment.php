<?php

class Attachment extends AttachmentAppModel {
    
    public function beforeSave() {
        if (!$this->id) {
            $this->data[$this->alias]['uid'] = $this->createUniqueId(); 
        }
        return true;
    }
    
    public function beforeDelete() {
        $data = $this->findById($this->id);
        $file = Configure::read('Attachment.storage.upload');
        $file .= str_replace('/', DS, $data[$this->alias]['path']);
        if (is_file($file)) {
            if (!unlink($file)) return false;
        }
        return true;
    }

    private function createUniqueId($column = 'uid', $length = 3) {
        $number = '';
        for ($i = 0; $i < $length; $i++) {
            $number .= mt_rand(100, 999);
        }
        if (!$this->hasAny(array("{$this->alias}.{$column}" => $number))) {
            return $number;
        } else {
            return $this->createUniqueId($column, $length);
        }
    }
}
