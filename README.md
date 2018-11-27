# Json Api CRUD Bundle

[![pipeline status](https://gitlab.trikoder.net/trikoder/jsonapibundle/badges/master/pipeline.svg)](https://gitlab.trikoder.net/trikoder/jsonapibundle/commits/master)
[![coverage report](https://gitlab.trikoder.net/trikoder/jsonapibundle/badges/master/coverage.svg)](https://gitlab.trikoder.net/trikoder/jsonapibundle/commits/master)

Package to provide out-of-the box support for jsonapi in symfony with as native as possible way.

# Under development
This package is currently under active development, but it is considered production safe.

## How to install
Guide how to setup bundle is located in [quick start guide](src/Resources/doc/getting_started/quick.md)

## Sample usage
For quick start see [quick start guide](src/Resources/doc/getting_started/quick.md)
You can also look at demo code located in [test suite](tests/Resources)

## Advanced usage
For advanced usages that are outside of generic api you get from quick start, see [advanced usage](src/Resources/doc/getting_started/advanced_usage.md)

## Requirements
Bundle is uses symfony 3 and doctrine.

## Documentation and examples

Example code can also be found in `tests/Resources/`.

Documentations is located inside `src/Resources/doc/` directory.

## Issues

Use gitlab issue tracker.

## Known limitations

- each jsonapi enabled action that receives arguments must receive Request as first argument

## Versioning and changelog

Project follows [Semantic versioning](http://semver.org/).

Change log for the project can be found in changelog.md

## Testing

The whole sandbox and development enviroment is located inside project.

To run tests, position yourself inside `tests/Resources/docker` and run `bin/test.sh`
This will build whole docker setup, load fixtures and run all test suites.

## Contributing

*TODO* write how people can send pull requests

For development of the package, we are using the same tools as for testing.
Position yourself inside `tests/Resources/docker` and run `bin/start.sh`
PHP cli commands can be run from same directory using `bin/console`.
There is also php access script `bin/php [CMD]` (eg. `bin/php bash` to enter bash).

### Coding standards

When contributing to this package, you will need to adhere to our conding standards.
They are following PSR-2 with some additional rules. To check your code during development
you can use provided config for php-cs-fixer. it's in root of the project, file:

`.php_cs.dist`

**Checking your code**

First you need to setup test enviroment (as described in paragraph above).

Then:

Checking code:

```
bin/php_cs --dry-run
```
If you want automatic fix, just ommit ``--dry-run`:

```
bin/php_cs
```
This will check and fix your code.


## Credits

Copyright (C) 2017 Trikoder

Author: Alen Pokos.

Contributors (in alphabetic order): Antonio Pauletich, Alen Pokos, Antonio Šunjić, Damir Trputec, Juraj Juričić, Krešo Kunjas, Petar Obradović, Vedran Krizek, Vedran Mihočinec

## License

Package is licensed under [MIT License](./LICENSE)
