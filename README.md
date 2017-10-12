# Json Api CRUD Bundle

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

## Credits

Copyright (C) 2017 Trikoder

Author: Alen Pokos.

## License

Package is licensed under [MIT License](./LICENSE)
