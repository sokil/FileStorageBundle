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

Read about Gaufrette et https://github.com/KnpLabs/Gaufrette.
Read abount configuring Gaufrette filesystems in Symfony at https://github.com/KnpLabs/KnpGaufretteBundle.
