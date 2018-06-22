### Riminder API PHP
🐘 Riminder API PHP Wrapper

-------------
## Installation with composer

```
composer require riminder/riminder-php-api
```

## Authentication

To authenticate against the api, get your API SECRET KEY from your riminder
dashboard:
![findApiSecret](./secretLocation.png)

Then create a new `Riminder` object with this key:
```php
require __DIR__ . '/vendor/autoload.php';

// Authentication to api
$riminderClient = new Riminder('yourShinnyKey');

// Now, you can use the api, Congrats !

```

## API Overview

```php
require __DIR__ . '/vendor/autoload.php';

// Authentication to api
$riminderClient = new Riminder('yourShinnyKey');

$profile = $riminderClient->profile->getProfile('102b6aa635fnf8ar70e7888ee63c0jde0c753dtg')
$name = $profile['name']
$profile_id = $profile['profile_id']

echo "Profile '$profile_id' is named '$name', a beautiful name actually"
```
# Errors
If an error occurs while an operation an exception of type `RiminderApiException` is raised.

## Methods & Resources
  For any methods that needs `*_id` and `*_reference`
  you need to provide at least one of them but not necessarily both.
* # filters
  * Get all filters for given team account :
  ```php
  RiminderClient->filter->getfilters()
  ```
  * Get the filter information associated with filter id :
  ```php
  RiminderClient->filter->getFilter($filter_id, $filter_reference)
  ```
* # Profiles
  * Retrieve the profiles information associated with some source ids :
  ```php
  RiminderClient->profile->getProfiles(array $args)
  ```
  `$args` is a array that contains all query parameters you need.
  * Add a resume to a sourced id :
  ```php
  RiminderClient->profile->postProfile($source_id, $file_path, $profile_reference, $timestamp_reception, $training_metadata)
  ```
  * Add all resume from a directory to a sourced id, use `$recurs` to enable recursive mode :
  ```php
  RiminderClient->profile->postProfiles($source_id, $file_path, $recurs, $timestamp_reception, $training_metadata)
  ```
  It returns an array like: `result[filename] = server_reponse`.
  Can throw `RiminderApiProfileUploadException`
  * Get the profile information associated with both profile id and source id :
  ```php
  RiminderClient->profile->getProfile($profile_id, $source_id, $profile_reference)
  ```
  * Retrieve the profile documents associated with both profile id and source id :
  ```php
  RiminderClient->profile->getDocuments($profile_id, $source_id, $profile_reference)
  ```
  * Retrieve the profile parsing data associated with both profile id and source id :
   ```php
   RiminderClient->profile->getParsing($profile_id, $source_id, $profile_reference)
   ```
  * Retrieve the profile scoring data associated with both profile id and source id :
   ```php
   RiminderClient->profile->getScoring($profile_id, $source_id, $profile_reference)
   ```
  * Edit the profile stage given a filter :
  ```php
  RiminderClient->profile->updateStage($profile_id, $source_id, $filter_id, $rating, $filter_reference, $profile_reference)
  ```
  * Edit the profile rating given a filter :
  ```php
  RiminderClient->profile->updateRating($profile_id, $source_id, $filter_id, $rating, $filter_reference, $profile_reference)
  ```
* # Sources
  * Get all sources for given team account:
  ```php
  RiminderClient->source->getSources()
  ```
  * Get the source information associated with source id:
   ```php
   RiminderClient->source->getSource($source_id)
   ```
* # webhook
This package supplies webhook support as well.
  * Check for webhook integration:
  ```php
  RiminderClient->webhook->check();
  ```
  * Set an handler for an event (listed with RiminderEvents constants)
  ```php
  RiminderClient->webhook->setHandler($eventName, $callback);
  ```
  * Check if the event has an handler
  ```php
  RiminderClient->webhook->isHandlerPresent($eventName);
  ```
  * Remove handler for an event
  ```php
  RiminderClient->webhook->removeHandler($eventName);
  ```
  * Handle the incoming webhook request, you need to put as argument HTTP_RIMINDER_SIGNATURE as an argument.
  ```php
  RiminderClient->webhook->handleRequest($encoded_datas);
  ```
* # Constants
  * `RiminderFields` Contains to fill profile's `args` array for /profiles constants.
  * `RiminderStage`  Contains profile stage constants.
  * `RiminderSort_by`  Contains sorting options constants.
  * `RiminderOrder_by`  Contains order options constants.
  * `RiminderSeniority`  Contains profile seniority constants.
  * `RiminderTraining_metadata`  Contain metadata fields for profile adding constants.
  * `RiminderEvents` Contains event names constans.
* # Exception
  * `RiminderApiException` parent of all thrown exception. Thrown when an error occurs.
  * `RiminderApiResponseException` thrown when response http code is not a valid one.
    * `getHttpCode()` to get the http code of the response.
    * `getHttpMessage()` to get the reason of response error.
  * `RiminderApiArgumentException` thrown when an invalid argument is pass to a method
  * `RiminderApiProfileUploadException` thrown when an error occurs during file upload.
    * `getFailedFiles()` to get not sended files list.
    * `getFailedFilesWithTheirExp()` to get not sended files with their exception (like: `exception_occured_during_tranfert = failed_file_list[filename]`)
    * `getSuccefullySendedFiles()` to get successfuly sended files with their response from server (like: `server_reponse_for_sucessful_upload = sucess_file_list[filename]`)

For details about method's arguments and return values see [api's documentation](https://developers.riminder.net/v1.0/reference#source)
