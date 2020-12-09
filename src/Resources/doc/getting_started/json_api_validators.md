# Validators
By default JsonApiBundle will use `SymfonyValidatorAdapter` to validate JSON request.

## Symfony validator
This validator uses `SymfonyValidatorAdapter` class.
This validator is going to validate following structures and mark them as valid:
```
{
  "data": {
    "type": "articles",
    "id": "1",
    "relationships": {
        "author": {
            "data": { "type": "people", "id": "9" }
        },
    }
  }
}
```
```
{
  "data": {
    "type": "articles",
    "id": "1",
    "relationships": {
        "author": {
            "data": [
                { "type": "people", "id": "9" },
                { "type": "people", "id": "10" }
            ]
        },
    }
  }
}
```

```
{
  "data": {
    "type": "articles",
    "id": "1",
    "relationships": {
        "author": {
            "data": { "id": "9" }
        },
    }
  }
}
```
```
{
  "data": {
    "type": "articles",
    "id": "1",
    "relationships": {
        "author": {
            "data": null
        },
    }
  }
}
```
```
{
  "data": {
    "type": "articles",
    "id": "1",
    "relationships": {
        "author": {
            "data": []
        },
    }
  }
}
```


## Relationship validators
This validator uses `RelationshipValidatorAdapter` class.
In order to use this validator you need to set it to action within controller, for example
```
/**
 * @Route("", name="some_name", methods={"POST"}, defaults={"_jsonapibundle_relationship_endpoint": true})
 */
public function sampleAction() {}
```

This validator is going to validate following structures and mark them as valid:

```
{
    "data": [
        { "type": "tags", "id": "2" },
        { "type": "tags", "id": "3" }
    ]
}
```