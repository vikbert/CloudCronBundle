<div align="center">
  <img src="docs/cron.svg" width=400/>
  <h3>Cloud Cron Bundle</h3>
  <p>A lightweight and super fast symfony bundle to schedule cron jobs in cloud environment, such as cloud foundry.</p>

  <p>
    <a href="#">
      <img src="https://img.shields.io/badge/PRs-Welcome-brightgreen.svg?style=flat-square" alt="PRs Welcome">
    </a>
    <a href="#">
      <img src="https://img.shields.io/badge/License-MIT-brightgreen.svg?style=flat-square" alt="MIT License">
    </a>
  </p>

  `composer require vikbert/cloud-cron-bundle`
</div>

---

> ⚠️ follow the steps to use the bundle in a symfony based application


## 1.a Install the bundle via "packagist"
```bash
composer require vikbert/cloud-cron-bundle
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
composer require vikbert/cloud-cron-bundle
```

## 2. Apply Doctrine migrations
```bash
php bin/console doctrine:migrations:diff
php bin/console doctrine:migrations:mi
```

## licence

[MIT](./LICENSE) License © 2021 [@vikbert](https://vikbert.github.io/)
