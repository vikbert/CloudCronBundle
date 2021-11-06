<div align="center">
  <img src="https://dev.azure.com/schwarzit/a93859d1-1284-447d-9b34-67bc9cd2f7e4/_apis/git/repositories/3a9cd60d-5dda-4af1-9e59-a1f9385e2e3c/items?path=/docs/cron.png&versionDescriptor%5BversionOptions%5D=0&versionDescriptor%5BversionType%5D=0&versionDescriptor%5Bversion%5D=main&resolveLfs=true&%24format=octetStream&api-version=5.0" width=100 alt="Project-Logo"/>
  <h3>Cloud Cron Bundle</h3>
  <p>A lightweight and super fast symfony bundle to schedule cron jobs in cloud environment, and it is designed to 
be used STACKIT.</p>

  <p>
    <a href="#">
      <img src="https://img.shields.io/badge/PRs-Welcome-brightgreen.svg?style=flat-square" alt="PRs Welcome">
    </a>
    <a href="#">
      <img src="https://img.shields.io/badge/License-MIT-brightgreen.svg?style=flat-square" alt="MIT License">
    </a>
  </p>

  `composer require chapterphp/cloud-cron-bundle`
</div>

---

# How to start the demo
in this demo, option 1.b is used to install the bundle via relative path
```bash
cd example

make start
```

# How to integrate the bundle
## 1.a Install the bundle via "packagist"
```bash
composer require chapterphp/cloud-cron-bundle
```

## 1.b Install the bundle via "repositories" in `composer.json` locally
> copy the bundle source to `src/bundles/CloudCronBundle`

```bash
# add the section to the composer.json
 "repositories": [
    {
      "type": "path",
      "url": "./bundles/CloudCronBundle"
    }
  ],

```

then, apply composer install
```bash
composer require chapterphp/cloud-cron-bundle
```

## 2. Apply Doctrine migrations
```bash
php bin/console doctrine:migrations:diff
php bin/console doctrine:migrations:mi
```

## 3. config the bundle
```bash
## config/packages/cloud_cron.yaml
cloud_cron:
    cron_watcher:
        # 60 repeats
        max_loop_limit: 60
        # 256 MB
        max_memory_limit: 256
        # 600 seconds
        max_time_limit: 600
```

# How to config STACKIT
```bash
## manifest.prod.yaml for STACKIT
    processes:
      - type: cron
        command: php bin/console cron:watch -vvv
        disk_quota: 1G
        health-check-type: process
        instances: 2
        memory: 256M
        timeout: 10
```

## licence

[apache-2.0](https://choosealicense.com/licenses/apache-2.0/)

## Author
xun.zhou@mail.schwarz
