<?php
  require __DIR__ . '/../vendor/autoload.php';

   $api = new Riminder("ask_ce813e1812ebeb663489abdad8b13aea");

   $start = 1494539999;
   $end =  1502488799;
   $seniority = "ALL";
   $source_ids = array("5823bc959983f7a5925a5356020e60d605e8c9b5","5823bc959983f7a5925a5356020e60d605e8c9b5");
   // $job_id = "a25bc879e774cc508706f6f4ddd8cce036689f3a";
   $job_id = null;
   $stage = null;
   $page = 1;
   $limit = 10;
   $sort_by = "RANKING";
      // var_dump($api->profile->getProfile([""], "ALL", "a25bc879e774cc508706f6f4ddd8cce036689f3a", 'YES', );
   var_dump($api->profile->getProfiles($source_ids, $start, $end, $page));
 ?>
