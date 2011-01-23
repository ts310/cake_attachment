<?php

class AttachmentHelper extends Helper {
    public $helpers = array('Html');

    /**
     * Render asset image tag
     * Creating resized cache off the fly to webroot folder
     * @return
     * @param object $id
     * @param object $options[optional]
     */
    public function embed($id, $options = array(), $htmlAttributes = array()) {
        $attachment = $this->_getAttachment($id);
        $source  = Configure::read('Attachment.storage.upload');
        $source .= $attachment['Attachment']['path'];
        // Check if file exists
        if (is_file($source)) {
            // Get pathinfo
            $pathinfo = pathinfo($source);
            // Image file
            if (in_array(low($pathinfo['extension']), array('jpeg', 'jpg', 'png', 'gif'))) {
                // Setting size from keyword
                $sizeKey = !empty($options['size']) ? low($options['size']) : 'original';
                // Setting cache file creation folder
                $cacheFolder = WWW_ROOT . 'files' . DS . 'attachments' . DS;
                $cacheFolder .= Inflector::underscore($attachment['Attachment']['object']) . DS;
                $cacheFolder .= Inflector::underscore($attachment['Attachment']['object_alias']) . DS;
                $cacheFolder .= str_replace('.', '-', $sizeKey) . DS;
                $timestamp = strtotime($attachment['Attachment']['created']);
                $cacheFolder .= date('Y', $timestamp). DS;
                $cacheFolder .= date('m', $timestamp). DS;
                $cacheFolder .= date('d', $timestamp). DS;
                // Generate file name
                $fileName = sprintf('%s.%s', $attachment['Attachment']['uid'], low($pathinfo['extension']));
                // Cached file path
                $cache = $cacheFolder . $fileName;
                // Checks if image needs to be regenerated
                $regenerate = false;
                if (!is_file($cache)) {
                    $regenerate = true;
                } else {
                    if (filesize($cache) == 0) $regenerate = true;
                    if (@filemtime($cache) < @filemtime($source)) $regenerate = true;
                }
                // Generating resized image file
                if ($regenerate) {
                    // Setting resize method and file name
                    $resizeOptions = $this->_determineSize($sizeKey);
                    $resize        = !empty($resizeOptions['resize']) ? $resizeOptions['resize'] : 'scale';
                    $size          = !empty($resizeOptions['size']) ? $resizeOptions['size'] : null;
                    // Check target folder
                    if (!is_dir($cacheFolder)) {
                        App::import('Core', 'Folder');
                        $Folder = new Folder();
                        $Folder->create($cacheFolder, 0777);
                    }
                    App::import('Component', 'Phpthumb.Phpthumb');
                    $Thumbnail = new PhpthumbComponent;
                    $output = $Thumbnail->create($resize, $source, $size, array());
                    $fp = fopen($cache, 'w');
                    fwrite($fp, $output);
                    fclose($fp);
                    chmod($cache, 0777);
                }
                // Setting src value
                $src = str_replace(WWW_ROOT, '', $cache);
                $src = str_replace(DS, '/', $src);
                $src = $this->Html->webroot($src);
                // if url only requested return src
                if (!empty($fullUrl)) $src = Router::url($src, true);
                if (!empty($url)) return $src;
                // Setting image tag attributes
                $htmlAttributes['src'] = $src;
                $htmlAttributes['alt'] = $htmlAttributes['title'] = $attachment['Attachment']['basename'];
                if (!empty($options['alt'])) {
                    $htmlAttributes['alt'] = $htmlAttributes['title'] = $options['alt'];
                }
                return $this->Html->tag('img', null, $htmlAttributes);
            }
        }
        return false;
    }

    public function url($id, $options) {
        $options = array_merge($options, array('url' => true));
        return $this->embed($id, $options);
    }

    /**
     * Get cached asset data
     * @param $id
     * @return unknown_type
     */
    private function _getAttachment($id = null) {
        App::import('Model', 'Attachment.Attachment');
        $Attachment = new Attachment();
        return $Attachment->findById($id);
    }

    /**
     * Getting size value from size keyword
     * @param $size
     * @return unknown_type
     */
    private function _determineSize($size) {
        if (strpos($size, '.')) {
            $size = explode('.', $size);
            if (!empty($size[0])) {
                $category = $size[0];
                $key = $size[1];
            }
        } else {
            $category = 'general';
            $key = $size;
        }
        $options = Configure::read('Attachment.cache.' . $category);
        if (array_key_exists($key, $options)){
            return $options[$key];
        }
        return false;
    }
}
