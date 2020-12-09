# Write actions flow in jsonapi

## Write process

Write process in jsonapi bundle is provided by Create, Update, UpdateRelationship and Delete  action traits.

## Create and update
Create and update of model follows similar flow with few differences in start and end points that are described in next chapter.

Process can be split into follwing steps:
1. get  the starting model (see differences)
2. use ModelInputHandler to merge request data with model
3. use ModelValidator to validate new model
4. send updated model to repository
5. return proper response (see differences)

## Differences - Create vs update
There are two differences in create and update actions:
1. starting model in create action is aquired from create factory while update action get it's from repository
2. create's proper response is redirect to show action while update will return updated resource

## UpdateRelationship
Adds or removes resources from relationship.

Process for both is same:
1. validate that relationship is allowed to be updated (by using `JsonApiConfig\UpdateRelationshipConfig`)
1. get starting model
1. use `\Trikoder\JsonApiBundle\Contracts\RelationshipRepositoryInterface` to add or remove resources from relationship
1. return `\Symfony\Component\HttpFoundation\Response` response if something went wrong or return model if it was successfully changed

## Delete
Delete action will use repository to delete loaded model.
