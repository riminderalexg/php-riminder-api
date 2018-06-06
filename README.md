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

$profile = $riminderClient->profile->get('102b6aa635fnf8ar70e7888ee63c0jde0c753dtg')
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
  RiminderClient->filter->get($filter_id, $filter_reference)
  ```
* # Profiles
  * Retrieve the profiles information associated with some source ids :
  ```php
  RiminderClient->profile->getProfiles(array $args)
  ```
  `$args` is a array that contains all query parameters you need.
  * Add a resume to a sourced id :
  ```php
  RiminderClient->profile->add($source_id, $file_path, $profile_reference, $timestamp_reception, $training_metadata)
  ```
  * Get the profile information associated with both profile id and source id :
  ```php
  RiminderClient->profile->get($profile_id, $source_id, $profile_reference)
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
   RiminderClient->source->get($source_id)
   ```
* # Constant
  * `RiminderClient->Fields` Contain constants to fill profile's `args` array
  * `RiminderClient->Stage`  Contain constants that represent profile stage.
  * `RiminderClient->Sort_by`  Contain constants that represent sorting options.
  * `RiminderClient->Order_by`  Contain constants that represent order options.
  * `RiminderClient->Seniority`  Contain constants that represent profile seniority.
  * `RiminderClient->Training_metadata`  Contain constants that represent metadata fields for profile adding.

For details about method's arguments and return values see [api's documentation](https://developers.riminder.net/v1.0/reference#source)
