# Larso View Blade

## Install
```
composer require larso/view
```

### Use
```
// index.php

use Larso\View\Blade;

require 'vendor/autoload.php';

$viewpath = __DIR__.'/views';
$cachepath = __DIR__.'/cache';

/**
 * @var \Illuminate\Contracts\View\Factory
 */
$blade = new Blade($viewpath, $cachepath);

echo $blade->make('hello')->with('name', 'YourName')->render();
```

### Use with Facade
```
// index.php

use Larso\View\Blade;

require 'vendor/autoload.php';

$viewpath = __DIR__.'/views';
$cachepath = __DIR__.'/cache';

new Blade($viewpath, $cachepath);

View::share('share', 'This value Share');
echo View::make('hello')->with('name', 'MY NAME');
```