# File attachment plugin for CakePHP

File attachment for CakePHP model

Usage:

- Create directories

		app/uploads/attachments  (For original file)
		app/webroot/files/attachments (For resized image file)

- Include configuration file.

		app/config/bootstrap.php

			require_once ROOT . DS . 'plugins' . DS . 'attachment' . DS . 'config' .
			DS . 'bootstrap.php';

- Add behavior to model  

		app/model/post.php

			class Post extends AppModel {
					$actsAs = array(
							'Attachment.Attachment' => array(
									'fields' => array('PostImage')
							)
					);
			}

- Add file field to post edit form.

		app/views/posts/edit.php

			echo $this->Form->input('PostImage', array('type' => 'file'));

- File will be saved to

		app/uploads/attachments/post/{yyyy}/{mm}/{dd}/post_image/{size}/{id}/{unique_number}.{extention}

