# Configuration

It can be done via controller annotations and global defaults.

## Glossary
For needs of this document, term Field means any model attribute or relation.

## Configuration directives

### model class
Model class that this controller is responsible of. Defaults to `\stdClass`.


### repository
Can be name of the service or reference to instance that implements any of these interfaces:
- `\Trikoder\JsonApiBundle\Contracts\RepositoryInterface`
- `\Trikoder\JsonApiBundle\Repository\RepositoryResolverInterface`
 
Defaults to built-in service `trikoder.jsonapi.doctrine_repository_factory` that is by default `Trikoder\JsonApiBundle\Bridge\Doctrine\RepositoryFactory`
that returns `Trikoder\JsonApiBundle\Bridge\Doctrine\DoctrineRepository`


### request body decoder
Must implement `\Trikoder\JsonApiBundle\Contracts\RequestBodyDecoderInterface`.

You can use this to redefine how regular json api body payload is transformed into controllers request body.
You can remap fields, change formats, etc.

Defaults to `\Trikoder\JsonApiBundle\Services\RequestDecoder\RequestBodyDecoderService` .


### fixed filtering
Array of fixed filtering defined for this controller. It is applied to all load action from repository (index, show, update, delete).
Defaults to empty array.

Example of fixed filtering would be `fixedFiltering={"customer":true}` as seen in `\Trikoder\JsonApiBundle\Tests\Resources\Controller\Api\User\CustomerController`


### allowed include paths
Which include paths are allowed in request params. [] for nothing is allowed, null for everything is allowed.
Defaults to null.


### allow extra params
Defines if extra params other than defined by jsonapi are allowed in the request params. Defaults to false.


### index

#### allowed sort fields
List of fields that is allowed to sort by.
[] for nothing is allowed, null for everything is allowed.
Defaults to null.


#### allowed filtering parameters
List of fields that is allowed to filter by.
[] for nothing is allowed, null for everything is allowed.
Defaults to null.

#### allowed fields
List of fields that can be requested in the api.
[] for nothing is allowed, null for everything is allowed.
Defaults to null.

#### default sort
Default sorting array. Defaults to [];

Example of default sort would be `defaultSort={"email":"desc", "id":"desc"}`

#### default pagination
Default pagination array. Defaults to [];

### create

#### factory
Used to generate new (empty) model to be used in create action.
Can be name of the service or reference to instance that implements any of these interfaces:
- `\Trikoder\JsonApiBundle\Model\ModelFactoryInterface`
- `\Trikoder\JsonApiBundle\Model\ModelFactoryResolverInterface`

Defaults to built in service `trikoder.jsonapi.simple_model_factory` that by default is `\Trikoder\JsonApiBundle\Model\Factory\SimpleModelFactory`

#### allowed fields
List of fields that are allowed to be sent in create payload.
[] for nothing is allowed, null for everything is allowed.
Defaults to null.

#### requred roles
Roles required to access create action.
[] for nothing is allowed, null for everything is allowed.
Defaults to null.

### update

#### allowed fields
List of fields that are allowed to be sent in update payload.
[] for nothing is allowed, null for everything is allowed.
Defaults to null.

#### requred roles
Roles required to access update action.
[] for nothing is allowed, null for everything is allowed.
Defaults to null.

### delete

#### requred roles
Roles required to access delete action.
[] for nothing is allowed, null for everything is allowed.
Defaults to null.



## Global configuration
Global configuration is done by defining `trikoder_json_api` section in application configuration.
Global defaults configuration example can be found in [example.yml](examples/example.yml).

## Controller annotations
Each controller can define it's own configuration by class annotation.

Example of annotation configuration can be found in [exampleAnnotation.php](examples/exampleAnnotation.php).


## Traits
The crud actions can be enabled by using traits that come with the bundle. 
Each action of CRUD has corresponding trait (or several of them for simple/specific behaviours).

### Retrieve

#### ShowActionTrait

#### IndexActionTrait

#### PaginatedActionTrait
 Incomplete implementation - to be implemented

### Create

#### CreateActionTrait

### Update

#### UpdateActionTrait

### Delete

#### DeleteActionTrait
