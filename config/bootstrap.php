<?php

// Attachments storage
Configure::write('Attachment.storage.upload', APP . 'uploads' . DS . 'attachments' . DS);
Configure::write('Attachment.storage.cache', WWW_ROOT . 'files' . DS . 'attachments' . DS);
Configure::write('Attachment.storage.convert', WWW_ROOT . 'files' . DS . 'convert' . DS);

Configure::write('Attachment.image.convert', array('jpeg', 'jpg', 'gif', 'png'));
Configure::write('Attachment.video.convert', array('mov', 'mpeg', 'mpg', 'avi', '3gp', 'wmv', 'mp4', 'flv'));

// Attachment filter option general option
Configure::write('Attachment.cache.general', array(
    'icon'          => array('size' => '16x16', 'resize' => 'crop'),
    'preview-small' => array('size' => '200x200', 'resize' => 'crop'),
    'preview'       => array('size' => '300x300', 'resize' => 'scale'),
    'xsmall'        => array('size' => '25x25', 'resize' => 'scale'),
    'small'         => array('size' => '50x50', 'resize' => 'scale'),
    'small-list'    => array('size' => '100x50', 'resize' => 'crop'),
    'medium'        => array('size' => '500x500', 'resize' => 'scale'),
    'large'         => array('size' => '800x800', 'resize' => 'scale'),
    'xlarge'        => array('size' => '1024x1024', 'resize' => 'scale'),
));
