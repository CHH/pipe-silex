# Pipe Service Provider for Silex

## Install

If you haven't got [Composer](http://getcomposer.org):

    wget http://getcomposer.org/composer.phar

Then install with:

    php composer.phar require chh/pipe-silex:*@dev

## Usage

### Configuration Options

* `pipe.root`: Use the default load path setup, based on this root
  directory. The paths `javascripts/`, `stylesheets/`,
  `vendor/javascripts` and `vendor/stylesheets` are added to your load
  path by default.
* `pipe.load_path` (default `[]`): Create a custom load path setup by setting this as
  array, e.g. `$app['pipe.load_path'] = [__DIR__ . '/css', __DIR__ . '/js']`.
* `pipe.use_precompiled` (default: `false`): Point links generated with
  the helpers to precompiled versions of assets.
* `pipe.precompile` (default: `['application.js', 'application.css']`):
  List of files which should be dumped to the filesystem when the
  `precompile` method was invoked.
* `pipe.precompile_directory`
  Output path for the `precompile` method.
* `pipe.js_compressor` (default: `''`):
  Javascript compressor for use on precompile. Available is `yuglify_js`
  or `uglify_js`.
* `pipe.css_compressor` (default: `''`):
  CSS compressor which is used on precompile, Available is only
  `yuglify_css`.
* `pipe.debug` (default: `false`):
  Skip compression of assets.
* `pipe.prefix` (default: `''`):
  Asset link prefix, which is used when precompiled assets are used.
* `pipe.manifest` (default: `''`):
  Location of the `manifest.json` created on precompile. Usually found
  inside the `pipe.precompile_directory`.

### Services

* `pipe`: Provides helper methods for precompiling and linking to
  assets.
  * `precompile()`:
    Precompiles assets set in `pipe.precompile` to static files which
    include a cache buster, and the `manifest.json`.
  * `assetLink($logicalPath)`:
    Creates a URL to the given asset. Respects `pipe.use_precompiled`,
    and either points to the dynamic asset generation, or to static files.
  * `assetLinkTag($logicalPath)`:
    Uses `assetLink` to create `<script>` or `<link>` tags depending on
    the Content Type of the asset.
* `pipe.environment`: `Pipe\Environment` instance which is used by the
  `pipe` service.

### Twig Extensions

* `pipe_link(logicalPath)`: Same as the `pipe` service's `assetLink`
  helper.
* `pipe_link_tag(logicalPath)`: Same as the service's `assetLinkTag`
  helper.

## License

The MIT License

Copyright (c) 2012 Christoph Hochstrasser

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.

