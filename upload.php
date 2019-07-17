<?php

    require_once("conn.php");
    require_once("./twitteroauth/twitteroauth/twitteroauth-master/twitteroauth/twitteroauth.php");
    mysqli_set_charset($conn, "utf8");

    if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on')
        $link = "https";
    else
        $link = "http";

    $link .= "://";
    $link .= $_SERVER['HTTP_HOST'];

    function getConnectionWithAccessToken($cons_key, $cons_secret, $oauth_token, $oauth_token_secret)
    {

        $connection = new TwitterOAuth($cons_key, $cons_secret, $oauth_token, $oauth_token_secret);
        return $connection;

    }

    if (isset($_POST["url"]) && isset($_POST["text"]) && isset($_POST["translated"])) {
        $twitterkeyword = $_SESSION['query'];
        $notweets = 100;
        $consumerkey = "KDWSx7Lsrtcd3qjZxwMiA8uaN";
        $consumersecret = "r8TRNHlxYW2fcla7X3bGcVrB5VpeouWcanbhpYN0tZRs6n6bSw";
        $accesstoken = "1046313089056952320-cM1FxJ8Y5tj9mHMJ1s1murNJW5GKrk";
        $accesstokensecret = "q159O2lZYJngerYzxB18y0aDRJWF4U9lGju7pjViLxO6c";

        $url = $_POST['url'];
        $text = $_POST['text'];
        $translated = $_POST['translated'];
        $lang = $_POST['lang'];
        $langcode = $_POST['langcode'];

        $connection = getConnectionWithAccessToken($consumerkey, $consumersecret, $accesstoken, $accesstokensecret);
        $tweets = $connection->get("https://api.twitter.com/1.1/search/tweets.json?q=" . $text ." -RT&count=100&tweet_mode=extended&lang=en&include_entities=true");
        $tweets = json_encode($tweets);
        $tweets = json_decode($tweets, true);
        $tweets = $tweets['statuses'];
        $count = 0;
        $final_array = [];

        for ($i = 0; $i < count($tweets); $i++) {
            if (strpos($tweets[$i]['full_text'], $text) !== false) {
                $array_form = explode(" ", $tweets[$i]['full_text']);
                $final_string = '';

                for ($i = 0; $i <  count($array_form); $i++) {
                    if (strpos($array_form[$i], "@") === false && strpos($array_form[$i], "#") === false && strpos($array_form[$i], "http") === false) {
                        $final_string .= $array_form[$i]." ";
                    }
                }

                if (strlen($final_string) < 300) {
                    array_push($final_array, $final_string);
                    $count++;
                }

                if ($count == 3) {
                    break;
                }
            }
        }

        $pretty_example = '';
        $pretty_translated = '<p>';

        for ($i = 0; $i < 3; $i++) {
            $translated_sent = file_get_contents($link.":5000/translateobj?q=".urlencode($final_array[$i])."&lang=".$langcode);
            $pretty_example .= "<p>".$final_array[$i]."</p>";
            $pretty_translated .= "<p>".$translated_sent."</p>";
        }

        $query = "INSERT INTO `vocab` (`image`, `text`, `translation`, `language`, `ex`, `tr`) VALUES ('".$url."', '"
            .$text."', '"
            .$translated."', '"
            .$lang."', '"
            .mysqli_real_escape_string($conn, $pretty_example)."', '"
            .mysqli_real_escape_string($conn, $pretty_translated)."')";

        if ($conn->query($query) === TRUE) {
            echo $query;
            echo "New record created successfully";
        } else {
            echo "Error: " . $query . "<br>" . $conn->error;
        }

        session_start();
        $_SESSION['uploaded'] = true;
    }