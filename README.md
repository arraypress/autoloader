# ArrayPress Autoloader

A version-aware autoloader manager for ArrayPress libraries that prevents conflicts when multiple versions of the same
library are loaded in a WordPress environment.

## Installation

```bash
composer require arraypress/autoloader
```

## Usage

In your WordPress plugin, after requiring the Composer autoload file:

```php
use ArrayPress\AutoloaderManager;

// Register your libraries
AutoloaderManager::register(
    'ArrayPress\\Geocoding\\', 
    '1.0.0',
    __DIR__ . '/vendor/arraypress/geocoding/src/'
);

// You can register multiple libraries
AutoloaderManager::register(
    'ArrayPress\\Utils\\',
    '2.1.0',
    __DIR__ . '/vendor/arraypress/utils/src/'
);
```

The AutoloaderManager will ensure that only the highest version of each library is loaded, preventing conflicts when
multiple plugins use different versions of the same library.

## Features

- Version-aware loading
- Prevents class redefinition conflicts
- PSR-4 compatible
- WordPress plugin friendly
- Supports multiple libraries
- Easy version management

## API

### Register a Library

```php
AutoloaderManager::register( string $namespace, string $version, string $baseDir ): void
```

### Check Registration Status

```php
AutoloaderManager::is_registered( string $namespace ): bool
```

### Get Library Version

```php
AutoloaderManager::get_version( string $namespace ): ?string
```

### Get All Registered Libraries

```php
AutoloaderManager::get_registered(): array
```

## Directory Structure

```
vendor/arraypress/autoloader/
├── src/
│   └── AutoloaderManager.php
├── composer.json
└── README.md
```

## Requirements

- PHP 7.4 or higher
- Composer

## License

GPL-2.0-or-later