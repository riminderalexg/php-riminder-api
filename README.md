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
```
require __DIR__ . '/vendor/autoload.php';

// Authentication to api
$riminderClient = new Riminder('yourShinnyKey');

// Now, you can use the api, Congrats !

```

## API Overview

```
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

* # Jobs
  * Get all jobs for given team account : `RiminderClient->job->getJobs()`
  * Get the job information associated with job id : `RiminderClient->job->get($source_id)`
* # Profiles
  * Retrieve the profiles information associated with some source ids : `RiminderClient->profile->getProfiles($source_ids, $date_start_timestamp, $date_end_timestamp, [$page, $limit, $sort_by, $seniority, $job_id, $stage])`
  * Add a resume to a sourced id : `RiminderClient->profile->add($source_id, $file, $profile_reference, $timestamp_reception)`
  * Get the profile information associated with both profile id and source id : `RiminderClient->profile->get($profile_id, $source_id)`
  * Retrieve the profile documents associated with both profile id and source id : `RiminderClient->profile->getDocuments($profile_id, $source_id)`
  * Retrieve the profile extractions associated with both profile id and source id : `RiminderClient->profile->getExtractions($profile_id, $source_id)`
  * Retrieve the profile jobs associated with both profile id and source id : `RiminderClient->profile->getJobs($profile_id, $source_id)`
  * Edit the profile stage given a job : `RiminderClient->profile->updateStage($profile_id, $source_id, $job_id, $rating)`
  * Edit the profile rating given a job : `RiminderClient->profile->updateRating($profile_id, $source_id, $job_id, $rating)`
* # Sources
  * Get all sources for given team account: `RiminderClient->source->getSources()`
  * Get the source information associated with source id: `RiminderClient->source->get($source_id)`

For details about method's arguments and return values see [api's documentation](https://developers.riminder.net/v1.0/reference#source)
