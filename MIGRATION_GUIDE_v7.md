# Migration Guide for updating from v6 to v7

1. Instead of calling `Podio::setup(..)` you should create an instance of `PodioClient` and pass it to other methods.
   ```php
    // Before:
    Podio::setup($client_id, $client_secret, $options);
    Podio::authenticate_with_password($username, $password);
    
    // After:
    $client = new PodioClient($client_id, $client_secret, $options);
    $client->authenticate_with_password($username, $password);
   ```
2. All operations take an instance of `PodioClient` as the first argument.
   ```php
   // Before:
   PodioItem::get(123);
   
   // After:
   PodioItem::get($client, 123);
   ```
3. All operations are now static methods on the model classes.
   ```php
    // Before:
    $item->save();
    
    // After:
    PodioItem::save($client, $item);
   ```
   Besides the `save` methods in almost all objects, the following methods have changed:
   ```php
    // Before: 
    $podioTask->completed();
    // After:
    PodioTask::complete($client, $podioTask->task_id);
    
    // Before:
    $podioTask->incompleted();
    // After:
    PodioTask::incomplete($client, $podioTask->task_id);
    
    // Before:
    $podioTask->destroy();
    // After:
    PodioTask::delete($client, $podioTask->task_id);
   ```

## Notes

The following regex might help you find all relevant places in your codebase:

```regex
Podio\w*::\w+\(|->save\(|->completed\(\)|->incompleted\(\)|->destroy\(\)
```

