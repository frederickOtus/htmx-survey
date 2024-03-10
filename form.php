<?php

if(!defined('INTERNAL')) {
    die();
}

class form {

    const MAX_SCORE = 10;
    const AVG_SCORE = 5;
    const DELIM = '|';

    public int $max_points;
    public int $current_points;
    public array $orgs;
    public string $error = "";

    public function __construct($request) {
        $this->orgs = [];
        $this->current_points = 0;

        foreach(explode("\n", file_get_contents("data/orgs")) as $org) {
            if(empty($org)) { continue; }
            [$name, $desc] = explode(self::DELIM, $org);
            $shortname = preg_replace('/\s+/', '', strtolower($name));
            $score = isset($request["$shortname"]) ? intval($request["$shortname"]) : 0;
            $score = min($score, self::MAX_SCORE);
            $score = max($score, 0);
            $this->current_points += $score;

            $this->orgs[$shortname] = [
                'name' => $name,
                'shortname' => $shortname,
                'score' => $score,
                'desc' => $desc,
            ];
        }

        $this->max_points = count($this->orgs) * self::AVG_SCORE;
    }

    public function get_remaining_pts() { return $this->max_points - $this->current_points; }
    
    public function increment($shortname) {
        if($this->max_points <= $this->current_points) { return; }

        if($this->orgs[$shortname]['score'] < self::MAX_SCORE) {
            $this->orgs[$shortname]['score'] += 1;
            $this->current_points += 1;
        }else {
            $this->error = "Cannot increment above max or overspend";
        }
    }
    
    public function decrement($shortname) {
        if($this->orgs[$shortname]['score'] > 0) {
            $this->orgs[$shortname]['score'] -= 1;
            $this->current_points -= 1;
        }else {
            $this->error = 'Cannot decrement past zero';
        }
    }

    public function check_overflow($shortname) {
        $overspend = $this->current_points - $this->max_points;
        if($overspend > 0) {
            if($overspend > $this->orgs[$shortname]['score']) {
                $this->error = "Cannot decrement";
            }
            $this->orgs[$shortname]['score'] -= $overspend;
            $this->current_points -= $overspend;
            $this->error .= "You tried to overflow!";
        }
    }

    public function can_decrement($org) { return $org['score'] > 0; }
    public function can_increment($org) { return $org['score'] < self::MAX_SCORE && $this->current_points < $this->max_points; }
    
    public function echo_form() {
        include('doc.php');
    }

    public static function get_name($force=false) {
        global $_COOKIE;
        // Get stamp from disk:
        $timestamp = trim(file_get_contents('data/timestamp'));
        $cookie = empty($_COOKIE['bread']) ? [] : json_decode($_COOKIE['bread'], true);

        if(empty($cookie) || $cookie['version'] != $timestamp || $force) {
            $oldname = '';
            if(!empty($cookie) && $cookie['version'] == true && $force) {
                $oldname = $cookie['name'];
            }

            // Read name from disk:
            $all_names = explode("\n", trim(file_get_contents('data/breads')));
            $used_names = explode("\n", trim(file_get_contents('data/used_breads')));
            $saved_names = array_column(json_decode(file_get_contents('data/results.json')), 'bread');

            $available = array_diff(array_diff($all_names, $used_names), $saved_names);

            if(count($available) == 0) {
                $name = "Anon #" . sizeof($used_names);
            }else {
                $randomKey = array_rand($available);
                $name = $available[$randomKey];
                $used_names[] = $name;
            }

            if(!empty($oldname)) {
                $used_names = array_diff($used_names, [$oldname]);
            }

            file_put_contents("data/used_breads", implode("\n", $used_names));

            $data = ['name' => $name, 'version' => $timestamp ];
            setcookie('bread', json_encode($data), time() + (86400 * 30), "/"); // 86400 = 1 day
            $_COOKIE['bread'] = json_encode($data);
            return $name;
        }

        return $cookie['name'];
    }

    public static function renew_name() {
        setcookie("bread", "", time() - 3600, "/");
        return self::get_name(true);
    }

    public static function has_submitted() {
        $name = self::get_name();
        $data = json_decode(file_get_contents('data/results.json'));
        foreach($data as $submission) {
            if($submission->bread == $name) {
                return true;
            }
        }

        return false;
    }

    public function save() {
        if(self::has_submitted()) {
            return false;
        }

        $response = $this->orgs;
        $response['bread'] = self::get_name();
        $data = json_decode(file_get_contents('data/results.json'), true);
        $data[] = $response;

        file_put_contents('data/results.json', json_encode($data));
    }

    public static function number_of_responses() {
        $data = json_decode(file_get_contents('data/results.json'), true);
        return sizeof($data);
    }

    public static function results() {
        $data = json_decode(file_get_contents('data/results.json'), true);
        $results = [];

        if(empty($data)) { return []; }

    }

}
