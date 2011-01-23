<?php

class AttachmentBehavior extends ModelBehavior {
    private $files = array();

    public function setup($model, $settings = array()) {
        $settings = (array)$settings;
        if (isset($settings['fields']) && !is_array($settings['fields'])) {
            $settings['fields'] = array($settings['fields']);
        }
        if (!empty($settings['fields'])) {
            foreach ($settings['fields'] as $key => $value) {
                $alias = Inflector::classify($value);
                $hasOne = array('hasOne' => array(
                     $alias=>array(
                        'className'  => 'Attachment.Attachment',
                        'foreignKey' => 'object_id',
                        'conditions' => array(
                            $value . '.object'       => $model->alias,
                            $value . '.object_alias' => $value
                        ),
                        'dependent' => true
                     )
                ));
                $model->bindModel($hasOne, false);
            }
        }
        $this->settings[$model->alias] = $settings;
    }

    public function beforeSave($model) {
        if (!empty($this->settings[$model->alias]['fields'])) {
            foreach ($this->settings[$model->alias]['fields'] as $key => $field) {
                // $field = Inflector::underscore($value);
                if (!empty($model->data[$model->alias][$field]['tmp_name'])) {
                    $this->files[$field] = $model->data[$model->alias][$field];
                }
            }
        }
        return true;
    }

    public function afterSave($model, $created) {
        if (!empty($this->files)) {
            $modelData = $model->findById($model->id);
            $rootFolder = Configure::read('Attachment.storage.upload');
            App::import('Model', 'Attachment.Attachment');
            $Attachment = new Attachment();
            foreach ($this->files as $key => $file) {
                $field = Inflector::underscore($key);
                // $foreignKey = $field . '_id';
                $attachmentId = null;
                $attachment = $Attachment->find('first', array(
                    'conditions'=>array(
                        'Attachment.object'       => $model->alias,
                        'Attachment.object_alias' => $key,
                        'Attachment.object_id'    => $model->id
                    )
                ));
                if ($attachment) {
                    $attachmentId = $attachment['Attachment']['id'];
                    // Remove previous file
                    if (is_file($rootFolder . $attachment['Attachment']['path'])) {
                        unlink($rootFolder . $attachment['Attachment']['path']);
                    }
                }
                if (!$attachmentId) {
                    $Attachment->create();
                    $attachment = $Attachment->save(array(
                        'object'       => $model->alias,
                        'object_alias' => $key,
                        'object_id'    => $model->id
                    ));
                    if ($attachment) {
                        $attachmentId = $Attachment->id;
                    }
                }
                if ($attachmentId) {
                    $timestamp = strtotime($modelData[$model->alias]['created']);
                    $folder = $rootFolder .
                        Inflector::underscore($model->alias) . DS .
                        date('Y', $timestamp) . DS .
                        date('m', $timestamp) . DS .
                        date('d', $timestamp) . DS .
                        $model->id . DS .
                        $field;
                    if (!is_dir($folder)) {
                        App::import('Core', 'Folder');
                        $Folder = new Folder();
                        $Folder->create($folder, 0777);
                    }
                    $fileInfo = pathinfo($file['name']);
                    $dest = $folder . DS . $attachmentId . '.' . low($fileInfo['extension']);
                    if (!move_uploaded_file($file['tmp_name'], $dest)) {
                        return $Attachment->delete($attachmentId);
                    }
                    $width = 0;
                    $height = 0;
                    if (in_array($fileInfo['extension'], Configure::read('Attachment.image.convert'))) {
                        $imgSize = getimagesize($dest);
                        $width   = $imgSize[0];
                        $height  = $imgSize[1];
                    }
                    $path = str_replace(DS, '/', str_replace($rootFolder, '', $dest));
                    $Attachment->id = $attachmentId;
                    $Attachment->save(array(
                        'basename' => $fileInfo['basename'],
                        'ext'      => $fileInfo['extension'],
                        'size'     => filesize($dest),
                        'width'    => $width,
                        'height'   => $height,
                        'path'     => $path
                    ));
                    @chmod($dest, 0777);
                    // if ($model->hasField($foreignKey)) {
                        // $model->data[$model->alias][$foreignKey] = $attachmentId;
                        // $saved = $model->save(
                            // array($foreignKey => $attachmentId),
                            // array('callbacks' => false, 'validate' => false)
                        // );
                    // }
                }
            }
        }
        return true;
    }
}
