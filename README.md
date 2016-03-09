# FileStorageBundle

Upload handling, registration of uploads to database, moving files to external filesystem.

[![Build Status](https://travis-ci.org/sokil/FileStorageBundle.svg?branch=master)](https://travis-ci.org/sokil/FileStorageBundle)

## Configuration

* Read more about uploading handler at https://github.com/sokil/php-upload
* Read about Gaufrette at https://github.com/KnpLabs/Gaufrette.
* Read abount configuring Gaufrette filesystems in Symfony at https://github.com/KnpLabs/KnpGaufretteBundle.

This bundle uses gaufrette as filesystem abstraction, so you need to configure filesystem in app config:
```yaml
knp_gaufrette:
    adapters:
        acme.attachments_adapter:
            local:
                directory:  "%kernel.root_dir%/attachments"
                create:     true
    filesystems:
        acme.attachments_filesystem:
            adapter: acme.attachments_adapter
```

You need tnet pass this filesystem name to your code. For example define parameter in extension of your bundle:

```php
<?php
namespace AcmeBundle\DependencyInjection;

class AcmeExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        if (isset($config['attachments_filesystem'])) {
            $container->setParameter(
                $this->getAlias() . '.attachments_filesystem',
                $config['attachments_filesystem']
            );
        }
    }
}
```

And then in application config:

```yaml

acme:
    attachments_filesystem: "acme.attachments_filesystem"
```

Now you may use file storage in your controller:
```php
<?php

// create upload handler from factory
$uploader = $this
    ->get('file_storage.upload_handler_factory')
    ->createUploadHandler([
        'fieldName' => 'attachment',
    ]);

// get file storage
$attachmentFilesystem = $this
    ->get('knp_gaufrette.filesystem_map')
    ->get($this->getParameter('acme.attachments_filesystem'));

$uploader->move($attachmentFilesystem);
```

### Uploader

This bundle uses uploading library [php-upload](http://github.com/sokil/php-upload) to handle uploads. 
Upload handler may be obtained from container through factory:

```php
<?php
// create upload handler from factory
$uploader = $this
    ->get('file_storage.upload_handler_factory')
    ->createUploadHandler([
        'fieldName' => 'attachment',
    ]);
    
// move uploaded file to external filesystem
$uploader->move($attachmentFilesystem);
```

If you need default handler, get in directly from container:

```php
<?php

// create upload handler from factory
$uploader = $this
    ->get('file_storage.upload_handler');
    
// move uploaded file to external filesystem
$uploader->move($attachmentFilesystem);
```

Or configure your own service with custom parameters:
```yaml
acme.attachment_upload_handler:
    class: Sokil\Upload\Handler
    factory:
      - '@file_storage.upload_handler_factory'
      - createUploadHandler
    arguments: 
      - {fieldName: 'attachment'},
```

More info you may find in documentation of [php-upload](http://github.com/sokil/php-upload).

### File writer

This bundle uses to move file into some external filesystem and add record to database about file.
First we need to create some file builder. This object allows to create instance of file entity and also 
content of stored file. File entity holds useful metadata about stored file. Use dafault file builder 
of uploaded file `file_storage.default_uploaded_file_builder` or configure your own service:
  
```yaml
acme.attachment_file_builder:
  class: Sokil\FileStorageBundle\FileBuilder\UploadedFileBuilder
  arguments:
    - '@acme.attachment_upload_handler'
```

Now you can use default file writer `file_storage.default_file_writer` or configure own:

```yaml
acme.attachment_file_writer:
class: Sokil\FileStorageBundle\FileWriter
arguments:
  - '@doctrine.orm.default_entity_manager'
  - '@knp_gaufrette.filesystem_map'
  - '@file_storage.path_strategy.chunk'
```

In controller you may write file to external filesystem

```php
<?php

$this
    ->get('acme.attachment_file_writer')
    ->write(
        $this->get('acme.attachment_file_builder'),
        'acme.attachments_filesystem'
    );
```

