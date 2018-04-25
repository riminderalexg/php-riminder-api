<?php
  require __DIR__ . '/../vendor/autoload.php';

   $api = new Riminder("ask_9322c036347fd33a3b23fec1e94fb1a8");

   $start =  new DateTime('2018-01-02');
   $end =  new DateTime();
   $seniority = "ALL";
   $source_ids = array('9ccd64e6a94b4e226667c290c4af28f2ea9b10b3',
    "622889612125eb5239ca959d8418815744ca3830",
    "ec7516b071fcae68558b0f0fed6040fbe2925c81");
   // $job_id = "a25bc879e774cc508706f6f4ddd8cce036689f3a";
   $job_id = null;
   $stage = null;
   $page = 1;
   $limit = 10;
   $sort_by = "RANKING";
      // var_dump($api->profile->getProfile([""], "ALL", "a25bc879e774cc508706f6f4ddd8cce036689f3a", 'YES', );
   var_dump($api->profile->getProfiles($source_ids, $start, $end, $page, $limit, $sort_by, $seniority, $job_id, $stage));
 ?>
