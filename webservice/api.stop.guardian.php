<?php

header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header('Access-Control-Allow-Methodhs: GET, POST, PUT');

require_once 'database.php';

/**
 * SBF API Class
 *
 */
class SBF_API {

    public $database;

    /**
     * Construction
     */
    public function __construct() {
        $this->database = new Database();
    }

    /**
     * Get bases and user info with route
     * @param type $route_id
     * @param type $oauth_user_id
     * @return type
     */

    public function getRouteBaseInfo($base_id){
      $data= array();
      $result = $this->database->query("SELECT * FROM sbfdm_route_base WHERE ID={$base_id}");
      if ($result->num_rows) {
          while ($row = $result->fetch_assoc()) {
              $data = $row;
          }
      }
      return $data;
    }

    public function getRouteBaseUser($route_id, $oauth_user_id) {
        $data = array("route" => NULL, "base" => array(), "user_route" => NULL, "user_base" => array());

        // get route info
        $result = $this->database->query("SELECT * FROM sbfdm_route WHERE ID={$route_id}");
        if ($result->num_rows) {
            $row = $result->fetch_assoc();
            $data["route"] = $row;
        }

        // get base info
        $result = $this->database->query("SELECT * FROM sbfdm_route_base WHERE route_id={$route_id}");
        if ($result->num_rows) {
            while ($row = $result->fetch_assoc()) {
                $data["base"][] = $row;
            }
        }

        // get route user info
        $result = $this->database->query("SELECT * FROM sbfdm_user_route WHERE route_id={$route_id} AND oauth_user_id='{$oauth_user_id}'");
        if ($result->num_rows) {
            while ($row = $result->fetch_assoc()) {
                $data["user_route"] = $row;
            }
        }

        // get base user info
        $result = $this->database->query("SELECT * FROM sbfdm_user_base WHERE route_id={$route_id} AND oauth_user_id='{$oauth_user_id}'");
        if ($result->num_rows) {
            while ($row = $result->fetch_assoc()) {
                $data["user_base"][] = $row;
            }
        }

        return $data;
    }

    /**
     * Get base and user relation info with base
     * @param type $base_id
     * @param type $oauth_user_id
     * @return type
     */
    public function getBaseUser($base_id, $oauth_user_id) {
        $data = array("base" => NULL, "user_base" => NULL);

        //remark for check
        //$this->checkGuardianMoreThanHour($base_id);

        // get base info
        $result = $this->database->query("SELECT * FROM sbfdm_route_base WHERE ID={$base_id}");
        if ($result->num_rows) {
            while ($row = $result->fetch_assoc()) {
                $data["base"] = $row;
            }
        }

        // get base user info
        $result = $this->database->query("SELECT * FROM sbfdm_user_base WHERE base_id={$base_id} AND oauth_user_id='{$oauth_user_id}'");
        if ($result->num_rows) {
            while ($row = $result->fetch_assoc()) {
                $data["user_base"] = $row;
            }
        }

        // check wait time - unlock-wait, challeng-wait
        if ($data["user_base"] != NULL) {
            //$today_time = strtotime(date("Y-m-d H:i:s"));
            $timezone  = 7; // GMT +7
            $today_time = gmdate("Y-m-d H:i:s", time() + 3600*($timezone/*+date("I")*/));

            $data["user_base"]["unlock_time"] = 0;
            $data["user_base"]["challenge_time"] = 0;
            $data["user_base"]["today_time"] = $today_time;

            if ($data["user_base"]["unlock_wait_time"] !== NULL) {
                $wait_time = strtotime($data["user_base"]["unlock_wait_time"]);
                $data["user_base"]["unlock_time"] = round((strtotime($today_time) - $wait_time) / 60, 1);
            }

            if ($data["user_base"]["challenge_wait_time"] !== NULL) {
                $wait_time = strtotime($data["user_base"]["challenge_wait_time"]);
                $data["user_base"]["challenge_time"] = round((strtotime($today_time) - $wait_time) / 60, 1);
            }
        }

        $data["base"]["guardian"] = NULL;
        if ($data["base"]["latest_guardian_oauth_user_id"] !== NULL) {
            $result = $this->database->query("SELECT * FROM sbfdm_oauth WHERE oauth_user_id='{$data["base"]["latest_guardian_oauth_user_id"]}'");
            if ($result->num_rows) {
                while ($row = $result->fetch_assoc()) {
                    $data["base"]["guardian"] = $row;
                }
            }
        }





        $data["route"] = NULL;
        $result = $this->database->query("SELECT * FROM sbfdm_route WHERE ID={$data["base"]["route_id"]}");
        if ($result->num_rows) {
            while ($row = $result->fetch_assoc()) {
                $data["route"] = $row;
            }
        }

        return $data;
    }

    function isMoreThanHour($dateStart, $dateStop){
        $hour = 3600;
        $d1 = strtotime($dateStart);
        $d2 = strtotime($dateStop);
        $diff = $d2 - $d1;
        if($diff>$hour){
            return true;
        }else{
            return false;
        }
    }


    function SecondDiff($dateStart, $dateStop){
        $hour = 3600;
        $d1 = strtotime($dateStart);
        $d2 = strtotime($dateStop);
        $diff = $d2 - $d1;
        return $diff;
    }


     function checkGuardianMoreThanHour($base_id){
      /**********
      check time at [sbfdm_route_base]
      Step : (if more than 1 hr)
      1. update [sbfdm_route_base] for latest guardian, score (to 0)
      2. update [sbfdm_user_base] guardian_status
      3. add score (min x score) at [sbf_user_score]
      ***********/
      $data=array();
      $guardian_oauth_id = "";
      $guardian_time = "";
      $guardian_score = 0;
      //$now = date('Y-m-d H:i:s');

      $timezone  = 7; // GMT +7
      $now = gmdate("Y-m-d H:i:s", time() + 3600*($timezone/*+date("I")*/));
      ///// sbfdm_route_base

      $sql = "select sbfdm_route_base.* from sbfdm_route_base ";
      $sql .= " where ID=$base_id";

      $result = $this->database->query($sql);

      if ($result->num_rows) {

          while ($row = $result->fetch_assoc()) {
              $guardian_oauth_id = $row['latest_guardian_oauth_user_id'];
              $guardian_score = $row['latest_guardian_score'];
              $guardian_time = $row['latest_guardian_date'];
          }

          $excessTime = $this->isMoreThanHour($guardian_time, $now);

          if($excessTime){

            //// update sbfdm_route_base
            $oneHour = date('Y-m-d H:i:s', strtotime('+1 hour', strtotime($guardian_time)));


            $sql = "update sbfdm_route_base set latest_guardian_date='$oneHour', latest_guardian_score=0 ";
            $sql .= " where ID=$base_id";
            $this->database->query($sql);
            $this->database->query('insert into debug (txt) values("203: ' . addslashes($sql) .  '") ');

            /// update sdfdm_user_base
            $sql = "update sbfdm_user_base set guardian_status = false , guardian_end_date = '$oneHour' where oauth_user_id='$oauth_user_id' and base_id=$base_id ";
            $this->database->query($sql);

            $guardian_seconds = $this->SecondDiff($now, $guardian_time);
            $guardian_minutes = ceil($guardian_seconds/60);
            if($guardian_minutes>60) $guardian_minutes = 60;

            $level = $this->getUserLevel($oauth_user_id);

            ///
            $perminute=1;
            $ql = "select score_per_minute from sbf_score_minute_guardian where level_num = $level ";
            $result = $this->database->query($sql);

            if ($result->num_rows) {
                while ($row = $result->fetch_assoc()) {
                  $perminute = $row['score_per_minute'];
                }
              }

              $score = $perminute * $guardian_seconds;

              $route_id=0;
              $ql = "select route_id from sbfdm_route_base where ID = $base_id ";
              $result = $this->database->query($sql);


              if ($result->num_rows) {
                  while ($row = $result->fetch_assoc()) {
                    $route_id = $row['route_id'];
                  }
                }

            //// update score
            $sql = "insert into sbf_users_score (oauth_user_id, action_id, level, score,txtReason, base_id, route_id)";
            $sql .= " values('$oauth_user_id', 5, $level, $score, 'ผลคูณ Guardian Time', $base_id, $route_id)";
            $result = $this->database->query($sql);

          }

      }

    }

    /**
     * Get n base unlock/lock status
     * @param type $base_ids
     * @param type $oauth_user_id
     * @return string
     */
    public function getNBaseUser($base_ids, $oauth_user_id) {
        $result = $this->database->query("SELECT base_id,unlocked_status FROM sbfdm_user_base WHERE base_id IN (".implode(",",$base_ids).")  AND oauth_user_id='{$oauth_user_id}'");
        $found = array();
        if ($result->num_rows) {
            while ($row = $result->fetch_assoc()) {
                $found[$row['base_id']] = $row["unlocked_status"];
            }
        }
        $data = array();
        foreach($base_ids as $key => $id){
            $data[$id] = "lock";
            if(isset($found[$id])){
            $data[$id] = "unlock";
            }
        }
        return $data;
    }

    /**
     * Get nearby bases with lat,lon
     * @param type $latitude
     * @param type $longitude
     * @return type
     */
    public function getNearbyRouteBase($latitude, $longitude) {
        $radius = 50.0;
        $distance_unit = 111.045;
        $data = array("nearby_base" => NULL);
        $sql = 'SELECT *
            FROM (
                SELECT z.ID,z.route_id,z.base_no,z.base_title,z.base_latitude, z.base_longitude,
                    p.radius,
                    p.distance_unit
                 * DEGREES(ACOS(COS(RADIANS(p.latpoint))
                 * COS(RADIANS(z.base_latitude))
                 * COS(RADIANS(p.longpoint - z.base_longitude))
                 + SIN(RADIANS(p.latpoint))
                 * SIN(RADIANS(z.base_latitude)))) AS distance
                    FROM sbfdm_route_base AS z
                    JOIN (
                        SELECT  ' . $latitude . '  AS latpoint,  ' . $longitude . ' AS longpoint,
                        ' . $radius . ' AS radius, ' . $distance_unit . ' AS distance_unit
                    ) AS p ON 1=1
                WHERE
                    z.base_latitude BETWEEN p.latpoint  - (p.radius / p.distance_unit)
                    AND p.latpoint  + (p.radius / p.distance_unit)
                AND
                    z.base_longitude BETWEEN p.longpoint - (p.radius / (p.distance_unit * COS(RADIANS(p.latpoint))))
                    AND p.longpoint + (p.radius / (p.distance_unit * COS(RADIANS(p.latpoint))))
            ) AS d
            WHERE distance <= radius
            ORDER BY distance';

        $result = $this->database->query($sql);
        if ($result->num_rows) {
            while ($row = $result->fetch_assoc()) {
                $data["nearby_base"][] = $row;
            }
        }
        return $data;
    }

    /**
     * Get unlocked base quiz
     * @param type $oauth_user_id
     * @return type
     */
    public function getBaseUnlockQuiz($oauth_user_id) {
        $data = array("quiz" => NULL);
        $result = $this->database->query("SELECT * FROM sbfdm_quiz WHERE category=1 ORDER BY RAND() LIMIT 1");
        if ($result->num_rows) {
            while ($row = $result->fetch_assoc()) {
                //unset($row["category"]);
                //unset($row["correct_answer"]); || for test only
                $data["quiz"] = $row;
            }
        }
        return $data;
    }

    /**
     * Check unlocked base quiz & answer
     * @param type $route_id
     * @param type $base_id
     * @param type $base_no
     * @param type $oauth_user_id
     * @param type $quiz_id
     * @param type $answer
     * @return string
     */
    public function checkBaseUnlockQuiz($route_id, $base_id, $base_no, $oauth_user_id, $quiz_id, $answer) {
        // prevent hack
        $data = array("correct" => 'false');
        $quiz = NULL;

        $result = $this->database->query("SELECT * FROM sbfdm_quiz WHERE ID='{$quiz_id}' LIMIT 1");
        if ($result->num_rows) {
            while ($row = $result->fetch_assoc()) {
                $quiz = $row;
            }
        }

        // base unlock successful
        $unlocked_status = 'false';
        $unlocked_date = NULL;
        $unlock_wait_time = NULL;

        if ($quiz['correct_answer'] == $answer) {
            $data["correct"] = 'true';
            $unlocked_status = 'true';


            //$time = new DateTime();
            //$unlocked_date = $time->format('Y-m-d H:i');

            $timezone  = 7; // GMT +7
            $unlocked_date = gmdate("Y-m-d H:i:s", time() + 3600*($timezone/*+date("I")*/));

            //// added by Over Sep 10,2017
            $user_level = $this->getUserLevel($oauth_user_id);
            $this->addScore(1 /*action: correct quiz*/, $user_level, $oauth_user_id, $route_id, $base_id );
            $this->addScore(2 /*action: Unlocked*/, $user_level, $oauth_user_id, $route_id, $base_id);

        } else {
            //$time = new DateTime();
            //$minutes_to_add = 3;
            //$time->add(new DateInterval('PT' . $minutes_to_add . 'M'));
            //$unlock_wait_time = $time->format('Y-m-d H:i');

            $timezone  = 7; // GMT +7
            $mytime = gmdate("Y-m-d H:i:s", time() + 3600*($timezone/*+date("I")*/));
            $minutes_add = '+3 minutes';
            $unlock_wait_time = date('Y-m-d H:i:s', strtotime($minutes_add, strtotime($mytime)));
        }

        $field = array(
            'oauth_user_id' => $oauth_user_id,
            'route_id' => $route_id,
            'base_id' => $base_id,
            'base_no' => $base_no,
            'unlocked_status' => $unlocked_status,
            'unlocked_date' => $unlocked_date,
            "unlock_wait_time" => $unlock_wait_time,
        );

        $result = $this->database->query("SELECT * FROM sbfdm_user_base WHERE oauth_user_id='{$oauth_user_id}' AND base_id='{$base_id}' LIMIT 1");
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $set = "";
            $index = 0;
            foreach ($field as $key => $value) {
                $set .= "{$key}='{$value}'";
                $index++;
                if ($index < count($field)) {
                    $set.=",";
                }
            }
            $sql = "UPDATE sbfdm_user_base SET {$set} WHERE ID='{$row["ID"]}'";
        } else {
            $key = implode(",", array_keys($field));
            $value = "'" . implode("','", array_values($field)) . "'";
            $sql = "INSERT INTO sbfdm_user_base({$key}) VALUES({$value})";
        }
        $result = $this->database->query($sql);

        return $data;
    }

    /**
     * Get base challenge quiz
     * @param type $oauth_user_id
     * @return type
     */
    public function getBaseChallengeQuiz($oauth_user_id, $session) {
        //// get corrected quiz id for this session
        $sql = "select quiz_id from sbf_users_quiz_correct where oauth_user_id = '${oauth_user_id}' and session='{$session}' ";
        $result = $this->database->query($sql);
        $ID = array();
        $not_in = "";
        if ($result->num_rows) {
            while ($row = $result->fetch_assoc()) {
                $ID[] = $row['quiz_id'];
            }

            $not_in = " and ID not in (" . implode(",", $ID) . ")";
        }


        $data = array("quiz" => NULL);
        //$result = $this->database->query("SELECT * FROM sbfdm_quiz WHERE category!=1 ORDER BY RAND() LIMIT 1");
        $result = $this->database->query("SELECT * FROM sbfdm_quiz WHERE 1=1 $not_in ORDER BY RAND() LIMIT 1");
        if ($result->num_rows) {
            while ($row = $result->fetch_assoc()) {
                //unset($row["category"]);
                //unset($row["correct_answer"]); || for test only
                $data["quiz"] = $row;
            }
        }
        return $data;
    }

    /**
     * Check base challenge quiz and answer
     * @param type $route_id
     * @param type $base_id
     * @param type $base_no
     * @param type $oauth_user_id
     * @param type $quiz_id
     * @param type $answer
     * @return string
     */
    public function checkBaseChallengeQuiz($route_id, $base_id, $base_no, $oauth_user_id, $quiz_id, $answer, $session) {
        // prevent hack
        $data = array("correct" => 'false');
        $quiz = NULL;
        $result = $this->database->query("SELECT * FROM sbfdm_quiz WHERE ID='{$quiz_id}' LIMIT 1");
        if ($result->num_rows) {
            while ($row = $result->fetch_assoc()) {
                $quiz = $row;
            }
        }
        // base challenge
        if ($quiz['correct_answer'] == $answer) {
            $data["correct"] = 'true';

            //// added by Over Sep 10,2017
            $user_level = $this->getUserLevel($oauth_user_id);
            $this->addScore(1 /*action: correct quiz*/, $user_level, $oauth_user_id , $route_id, $base_id);

            /// insert correct question , prevent random same old question
            $sql = "insert into sbf_users_quiz_correct ( oauth_user_id, session, quiz_id) values ( '{$oauth_user_id}', '{$session}', $quiz_id)";
            $this->database->query($sql);



        } else {
            ///// Incorrect!!!

            //// delete correct question for this session
            $sql = "delete from sbf_users_quiz_correct where oauth_user_id='($oauth_user_id)' ";
            $this->database->query($sql);

            /*
            $time = new DateTime();
            $minutes_to_add = 3;
            $time->add(new DateInterval('PT' . $minutes_to_add . 'M'));
            $challenge_wait_time = $time->format('Y-m-d H:i');
            */

            $timezone  = 7; // GMT +7
            $mytime = gmdate("Y-m-d H:i:s", time() + 3600*($timezone/*+date("I")*/));
            $minutes_add = '+3 minutes';
            $challenge_wait_time = date('Y-m-d H:i:s', strtotime($minutes_add, strtotime($mytime)));


            $sql = "UPDATE sbfdm_user_base SET challenge_wait_time='$challenge_wait_time' WHERE oauth_user_id='{$oauth_user_id}' AND base_id='{$base_id}'";
            $result = $this->database->query($sql);



        }
        return $data;
    }

    public function setStopGuardian($base_id){

      /// get latest guardian for this base
      $sql = "SELECT * FROM sbfdm_user_base WHERE base_id={$base_id} order by guardian_start_date desc ";
      $result = $this->database->query($sql);
      if ($result->num_rows >0 ) {
        $row = $result->fetch_assoc();
        $oauth_user_id = $row['oauth_user_id'];

        /// chkeck absolutely DEAD guardian
        $sql = "SELECT * FROM sbfdm_user_base WHERE guardian_start_date is NOT null and base_id={$base_id} and oauth_user_id='{$oauth_user_id}' ";
        $result = $this->database->query($sql);
        if ($result->num_rows >0 ) {
          //// Ever be guardian

          $sql = "SELECT * FROM sbfdm_user_base WHERE guardian_status='false' and guardian_end_date is NOT null and base_id={$base_id}";
          $result = $this->database->query($sql);
          if ($result->num_rows >0 ) {
            /// Have Albsolutely DEAD guardian , do nothing
          }else{
            /// Not set or should re-set end date of guardian
            $timezone  = 7; // GMT +7
            $now = gmdate("Y-m-d H:i:s", time() + 3600*($timezone/*+date("I")*/));

            $sql = "update sbfdm_user_base set guardian_status='false' , guardian_end_date='{$now}' where base_id={$base_id} and oauth_user_id='{$oauth_user_id}' ";
            $this->database->query($sql);

            //// calculate diff and record , if more than 60 minutes , set to 60
            $sql = "select guardian_start_date from sbfdm_user_base where base_id={$base_id} and oauth_user_id='{$oauth_user_id}' ";
            $result = $this->database->query($sql);
            $row = $result->fetch_assoc();
            $guardian_start_date = $row['guardian_start_date'];

            $dStart = date_create($guardian_start_date);
            $dStop = date_create($now);
            $diff = date_diff($dStop, $dStart);
            $minutes = $diff->format('%i') + 0; // minutes
            if($minutes>60){
              $minuets=60;

              $sql = "update sbfdm_route_base set latest_guardian_score=0 , latest_guardian_date='{$now}' , latest_guardian_oauth_user_id='{$oauth_user_id}' where ID={$base_id}";
              $this->database->query($sql);
            }

            ///// Set!!
            $sql = "update sbfdm_user_base set guardian_end_date={$now} and guardian_minute={$minutes} where base_id={$base_id} and oauth_user_id='{$oauth_user_id}' ";
            $this->database->query($sql);
          }
        }
      }
    }

    /**
     * Check base challenge win or lose
     * @param type $base_id
     * @param type $oauth_user_id
     * @param type $score
     */
    public function setBaseUser($base_id, $oauth_user_id, $score) {
        $challenge = 'lose';
        $result = $this->database->query("SELECT * FROM sbfdm_route_base WHERE ID='{$base_id}' LIMIT 1");
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if ($score > $row['latest_guardian_score']) {
                $challenge = 'win';
                $timezone  = 7; // GMT +7
              $now = gmdate("Y-m-d H:i:s", time() + 3600*($timezone/*+date("I")*/));
                $field = array(
                    'latest_guardian_score' => $score,
                    'latest_guardian_oauth_user_id' => $oauth_user_id,
                    'latest_guardian_date' => $now,
                );

                $set = "";
                $index = 0;

                foreach ($field as $key => $value) {
                    $set .= "{$key}='{$value}'";
                    $index++;
                    if ($index < count($field)) {
                        $set.=",";
                    }
                }
                $sql = "UPDATE sbfdm_route_base SET {$set} WHERE ID='$base_id}'";
                $this->database->query($sql);

                $this->database->query('insert into debug (txt) values("551: ' . addslashes($sql) .  '") ');
            }
        }

        $this->database->query('insert into debug(txt) values("559: check challenge = ' . $challenge . ' ") ');

        // user_base
        if ($challenge == 'win') {
            $timezone  = 7; // GMT +7
            $now = gmdate("Y-m-d H:i:s", time() + 3600*($timezone/*+date("I")*/));

            //// Select latest guardian to mark END
            $id_ = 0;
            $sql = "SELECT * FROM sbfdm_user_base WHERE base_id = {$base_id} AND guardian_status = 'true' ORDER BY guardian_start_date DESC LIMIT 1";
            $result = $this->database->query($sql);
            if ($result->num_rows > 0) {
              $row = $result->fetch_assoc();
              $ged = trim($row['guardian_end_date'] . "");
              if($ged == ""){ /// update onlye not have end date , end date was set by 1 hour sometime
                $id_ = $row['ID'];
              }
            }
            $this->database->query('insert into debug (txt) values("573: Guardian end date = ' . $ged . ' ") ');
            if($id_ > 0){
              //// Save guardian end date for calculate guardian minutes
              $sql = "update sbfdm_user_base set guardian_end_date = '$now' where ID = {$id_} ";
              $this->database->query($sql);

              $this->database->query('insert into debug (txt) values("578: New guardian , can save end time ") ');
            }else{
              $this->database->query('insert into debug (txt) values(\'580: New guardian but no ID_ \') ');
            }

            /// clear all guardian of this base to false
            $sql = "UPDATE sbfdm_user_base SET guardian_status='false' WHERE base_id='$base_id}'";
            $this->database->query($sql);



            $field = array(
                'guardian_status' => 'true',
                'guardian_score' => $score,
                'guardian_start_date' => $now /*(date('Y-m-d H:i:s')*/,
            );
            $set = "";
            $index = 0;
            foreach ($field as $key => $value) {
                $set .= "{$key}='{$value}'";
                $index++;
                if ($index < count($field)) {
                    $set.=",";
                }
            }
            //$time = new DateTime();
            //$minutes_to_add = 3;
            //$time->add(new DateInterval('PT' . $minutes_to_add . 'M'));
            //$challenge_wait_time = $time->format('Y-m-d H:i');
            $timezone  = 7; // GMT +7
            $mytime = gmdate("Y-m-d H:i:s", time() + 3600*($timezone/*+date("I")*/));
            $minutes_add = '+3 minutes';
            $challenge_wait_time = date('Y-m-d H:i:s', strtotime($minutes_add, strtotime($mytime)));

            /*** If WIN do not waite
            $sql = "UPDATE sbfdm_user_base SET {$set},challenge_wait_time='{$challenge_wait_time}' WHERE base_id='$base_id}' AND oauth_user_id='{$oauth_user_id}'";
            $this->database->query($sql);
            */

            //// Update new score to Route_Base
            $sql = "update sbfdm_route_base set latest_guardian_score = $score where ID=$base_id ";
            //$this->database->query('insert into debug (txt) values( "'  . $sql . '")');
            $this->database->query($sql);
            $this->database->query('insert into debug (txt) values("615: ' . addslashes($sql) .  '") ');

            //// added by Over Sep 10,2017
            $user_level = $this->getUserLevel($oauth_user_id);
            $this->addScore(100 /*action: Be Guardian*/, $user_level, $oauth_user_id, $route_id, $base_id);
        } else {
            //$time = new DateTime();
            //$minutes_to_add = 3;
            //$time->add(new DateInterval('PT' . $minutes_to_add . 'M'));
            //$challenge_wait_time = $time->format('Y-m-d H:i');
            $timezone  = 7; // GMT +7
            $mytime = gmdate("Y-m-d H:i:s", time() + 3600*($timezone/*+date("I")*/));
            $minutes_add = '+3 minutes';
            $challenge_wait_time = date('Y-m-d H:i:s', strtotime($minutes_add, strtotime($mytime)));

            $sql = "UPDATE sbfdm_user_base SET challenge_wait_time='{$challenge_wait_time}' WHERE base_id='$base_id}' AND oauth_user_id='{$oauth_user_id}'";
            $this->database->query($sql);
        }
    }

    /***********************************
    ************************************
    Functions added by Over since SEP 10, 2017
    ************************************
    ***********************************/

    public function addScore($action_id, $level_id, $oauth_user_id, $route_id=0 , $base_id=0){
      $now = date('Y-m-d H:i:s');
      $score=0;
      $reason = "";

      $sql = "select sbf_score_main.score_value as score , sbf_action.action_name_en as coz ";
      $sql .= " FROM sbf_score_main ";
      $sql .= " inner join sbf_action on sbf_score_main.action_id = sbf_action.action_id ";
      $sql .= " WHERE sbf_score_main.action_id='{$action_id}'  and sbf_score_main.level_num='{$level_id}' ";
      $sql .= " LIMIT 1";

      $result = $this->database->query($sql);
      if ($result->num_rows) {
          while ($row = $result->fetch_assoc()) {
              $score = $row['score'];
              $reason = $row['coz'];
          }

          $sql = "insert into sbf_users_score ";
          $sql .= " (oauth_user_id, action_id, level, score, txtReason, route_id, base_id, cdate) ";
          $sql .= " values('{$oauth_user_id}' , $action_id, $level_id, $score , '$reason', $route_id, $base_id,  '$now')";
          $this->database->query($sql);
      }
    }

    public function getUserLevel($oauth_user_id){
      $level = 1;
      $sql = "select level_num as lv from sbf_users_level_log where oauth_user_id='{$oauth_user_id}' ";
      $sql .=" order by cdate desc limit 1";
    //$this->database->query("insert into debug(txt) values('$sql')");

      $result = $this->database->query($sql);
      if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $level = $row['lv'];
      }
      return $level;

    }
    public function getUserUnlockedBaseName($oauth_user_id){
      $data=array();
      $sql = "select sbfdm_route_base.base_title,sbfdm_user_base.base_id from sbfdm_route_base inner join sbfdm_user_base on sbfdm_route_base.ID = sbfdm_user_base.base_id where sbfdm_user_base.oauth_user_id like '{$oauth_user_id}' and sbfdm_user_base.unlocked_status= 'true' ";
      $result = $this->database->query($sql);
      if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
          $data[] = $row;
        }
      }
      return $data;
    }

    public function getLatestTenScoreHistory($oauth_user_id){
      $data=array();
      $sql = "SELECT sbf_users_score.score,sbf_users_score.cdate, sbf_action.action_name_en FROM sbf_users_score inner JOIN sbf_action on sbf_users_score.action_id = sbf_action.action_id WHERE oauth_user_id = '{$oauth_user_id}' order by cdate desc limit 10 ";

      $result = $this->database->query($sql);
      if ($result->num_rows > 0) {
        $i=0;
        while ($row = $result->fetch_assoc()) {
          $date = date_create($row['cdate']);
          $dateformated = date_format($date, 'M d,Y');

          $data[$i]['num'] = $i;
          $data[$i]['score'] = $row['score'];
          $data[$i]['text'] = $row['action_name_en'];
          $data[$i]['date'] = $dateformated;

          $i++;
        }
      }
      return $data;
    }

    public function getUserScoreLevel($oauth_user_id){
      $data = array('score' => NULL, 'level'=> NULL, 'unlocked_num'=>NULL);
      $score=0;
      $sql = "select sum(score) as s from sbf_users_score where oauth_user_id = '{$oauth_user_id}' ";
      $result = $this->database->query($sql);
      if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $score = $row['s'];
      }
      $data['score'] = $score;

      $level = 1;
      $sql = "select level_num as lv from sbf_users_level_log where oauth_user_id='{$oauth_user_id}' ";
      $sql .=" order by cdate desc limit 1";
      $result = $this->database->query($sql);
      if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $level = $row['lv'];
      }
      $data['level'] = $level;

      $unlocked=0;
      $sql = "select count(route_id) as cc from sbfdm_user_base where oauth_user_id='{$oauth_user_id}' and unlocked_status = 'true' ";
      $result = $this->database->query($sql);
      if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $unlocked = $row['cc'];
      }
      $data['unlocked_num'] = $unlocked;

      return $data;
    }

    public function setFavRoute($route_id, $oauth_user_id){
      $isFav = false;
      $haveRow = false;
      $now = date('Y-m-d H:i:s');
      $sql = "select favorite_status from sbfdm_user_route where route_id = $route_id and oauth_user_id='$oauth_user_id' ";



      $this->database->query("insert into debug (txt) values('736: " . addslashes($sql) . "')");


      $result = $this->database->query($sql);
      if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $isFav = $row['favorite_status'];
        $haveRow = true;
      }

      $NewBoolean='false';
      if($isFav) {$NewBoolean = 'false';} else {$NewBoolean='true';}

      if($haveRow){
        $sql = "update sbfdm_user_route set favorite_status = '$NewBoolean' , udate='$now' where route_id = $route_id and oauth_user_id='$oauth_user_id' ";
      }else{
        $sql = "insert into sbfdm_user_route (route_id, oauth_user_id, favorite_status, cdate, udate) ";
        $sql .= " values($route_id, '$oauth_user_id', '$NewBoolean' , '$now', '$now') ";
      }
      $this->database->query($sql);

      $data['sql'] = $sql;
      return $data;
    }

    function generateRandomString($length = 16) {
			$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
			$charactersLength = strlen($characters);
			$randomString = '';
			for ($i = 0; $i < $length; $i++) {
					$randomString .= $characters[rand(0, $charactersLength - 1)];
			}
			return $randomString;
  	}

}/// end class

/**
 * Main Program
 */
$sbf_api = new SBF_API();

if ($_POST['method'] == "get_route_base_user") {
    $response = $sbf_api->getRouteBaseUser($_POST['route_id'], $_POST['oauth_user_id']);
    header('Content-Type: application/json');
    echo(json_encode($response));
} else if ($_POST['method'] == "get_base_user") {
    $response = $sbf_api->getBaseUser($_POST['base_id'], $_POST['oauth_user_id']);
    header('Content-Type: application/json');
    echo(json_encode($response));
} else if ($_POST['method'] == "get_nbase_user") {
    $response = $sbf_api->getNBaseUser($_POST['base_ids'], $_POST['oauth_user_id']);
    header('Content-Type: application/json');
    echo(json_encode($response));
} else if ($_POST['method'] == "get_nearby_route_base") {
    $response = $sbf_api->getNearbyRouteBase($_POST['sbf_current_gps']['latitude'], $_POST['sbf_current_gps']['longitude']);
    header('Content-Type: application/json');
    echo(json_encode($response));
} else if ($_POST['method'] == "get_base_unlock_quiz") {
    $response = $sbf_api->getBaseUnlockQuiz($_POST['oauth_user_id']);
    header('Content-Type: application/json');
    echo(json_encode($response));
} else if ($_POST['method'] == "check_base_unlock_quiz") {
    $response = $sbf_api->checkBaseUnlockQuiz($_POST['route_id'], $_POST['base_id'], $_POST['base_no'], $_POST['oauth_user_id'], $_POST['quiz_id'], $_POST['answer']);
    header('Content-Type: application/json');
    echo(json_encode($response));
} else if ($_POST['method'] == "get_base_challenge_quiz") {
    $response = $sbf_api->getBaseChallengeQuiz($_POST['oauth_user_id'], $_POST['session']);
    header('Content-Type: application/json');
    echo(json_encode($response));
} else if ($_POST['method'] == "check_base_challenge_quiz") {
    $response = $sbf_api->checkBaseChallengeQuiz($_POST['route_id'], $_POST['base_id'], $_POST['base_no'], $_POST['oauth_user_id'], $_POST['quiz_id'], $_POST['answer'], $_POST['session']);
    header('Content-Type: application/json');
    echo(json_encode($response));
} else if ($_POST['method'] == "set_base_user") {
    $response = $sbf_api->setBaseUser($_POST['base_id'], $_POST['oauth_user_id'], $_POST['score']);
    header('Content-Type: application/json');
    echo(json_encode($response));
} else if ($_POST['method'] == "get_user_score_level") {
    $response = $sbf_api->getUserScoreLevel($_POST['oauth_user_id']);
    header('Content-Type: application/json');
    echo(json_encode($response));
}else if ($_POST['method'] == "set_fav_route") {
    $response = $sbf_api->setFavRoute($_POST['route_id'],$_POST['oauth_user_id']);
    header('Content-Type: application/json');
    echo(json_encode($response));
}else if ($_POST['method'] == "get_user_unlocked_base_name") {
    $response = $sbf_api->getUserUnlockedBaseName($_POST['oauth_user_id']);
    header('Content-Type: application/json');
    echo(json_encode($response));
}else if ($_POST['method'] == "get_latest_ten_score_history") {
    $response = $sbf_api->getLatestTenScoreHistory($_POST['oauth_user_id']);
    header('Content-Type: application/json');
    echo(json_encode($response));
}else if ($_POST['method'] == "get_route_base_info") {
    $response = $sbf_api->getRouteBaseInfo($_POST['base_id']);
    header('Content-Type: application/json');
    echo(json_encode($response));
}else if ($_POST['method'] == "set_stop_guardian") {
    $response = $sbf_api->setStopGuardian($_POST['base_id'], $_POST['oauth_user_id']);
    header('Content-Type: application/json');
    echo(json_encode($response));
}
