# FileStorageBundle

[![Build Status](https://travis-ci.org/sokil/FileStorageBundle.svg?branch=master)](https://travis-ci.org/sokil/FileStorageBundle)

## Cofiguration

* Read more about uploading handler at https://github.com/sokil/php-upload
* Read about Gaufrette at https://github.com/KnpLabs/Gaufrette.
* Read abount configuring Gaufrette filesystems in Symfony at https://github.com/KnpLabs/KnpGaufretteBundle.

First define attribute in extension of your bundle:
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

Then configure filesystem in app config:
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

acme:
    attachments_filesystem: "acme.attachments_filesystem"
```

Then in controller:
```php
<?php

// get file storage
$uploader = $this
    ->get('file_storage.handler_factory')
    ->createUploadHandler([
        'fieldName' => 'attachment',
    ]);

$attachmentFilesystem = $this
    ->get('knp_gaufrette.filesystem_map')
    ->get($this->getParameter('acme.attachments_filesystem'));

$uploader->move($attachmentFilesystem);

