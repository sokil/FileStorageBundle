# FileStorageBundle

## Useage

Simple uploading:

```php
<?php
// create handler in factory
$uploader = $this
    ->get('file_storage.handler_factory')
    ->createUploadHandler([
        'fieldName' => 'attachment',
    ]);

// get gaugrette filesystem
$filesystem = new \Gaufrette\Filesystem(new \Gaufrette\Adapter\Local(
    this->getParameter('kernel.root_dir') . '/attachments/'
));

// upload
$uploader->move($filesystem);
```

Read abount configuring Gaufrette filesystems at https://github.com/KnpLabs/Gaufrette.
