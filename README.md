# FileStorageBundle

Working with different filesystems, managing file metadata in Doctrine ORM

[![Build Status](https://travis-ci.org/sokil/FileStorageBundle.svg?branch=master)](https://travis-ci.org/sokil/FileStorageBundle)

## Configuration

* Read about Gaufrette at https://github.com/KnpLabs/Gaufrette.
* Read abount configuring Gaufrette filesystems in Symfony at https://github.com/KnpLabs/KnpGaufretteBundle.

## See also
* See how to easy handle uploaded files at https://github.com/sokil/php-upload

## Configuration of supported filesystems
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

You need then pass this filesystem name to your code. For example define parameter in extension of your bundle:

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

Now you may use configured filesystems in your controller:
```php
<?php

$attachmentFilesystem = $this
    ->get('knp_gaufrette.filesystem_map')
    ->get($this->getParameter('acme.attachments_filesystem'));

```

### Move local file to filesystem

This bundle usefull for moving local files into some external filesystems and add record to database about file.
First we need to create some file entity. File entity holds useful metadata about stored file.

```php
<?php

$file = new File();
$file
    ->setName('some.txt)
    ->setSize(4242)
    ->setCreatedAt(new \DateTime())
    ->setMime('plain/text')
    ->setHash('some_hash_of_file_content');
            
$this
    ->get('file_storage')
    ->write(
        $file,
        'acme.attachments_filesystem',
        'some content of file'
    );
    
$fileId = $file->getId(); // now you have id of file
```

