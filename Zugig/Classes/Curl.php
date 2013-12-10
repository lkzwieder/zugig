<?php
class Curl {
    public static function createCurlHandler($url, $params = array(), $method = "GET", $headers = false) {
        foreach($params as $key => $value) {
            if(!is_string($value)) {
                $params[$key] = json_encode($value);
            }
        }

        $params = http_build_query($params);
        if($method == "GET") {
            $url = $params ? $url.'?'.$params : $url;
            $ch = curl_init($url);
        } else {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        }
        if($headers) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // This is a PHP level timeout. It does not matter the status of the connection.
        curl_setopt($ch, CURLOPT_TIMEOUT, 5 * 60); // 5 minutes so can't hung forever.
        return $ch;
    }

    public static function makeCurlRequest($ch) {
        try {
            $content = curl_exec($ch);
            $ans = self::processCurlAnswer($ch, $content);
            curl_close($ch);
            return $ans;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function makeCurlRequestsInParallel(array $curlHandlers) {
        try {
            $mh = curl_multi_init();
            if ($mh === false) {
                throw new Exception("Error creating a cURL multi handle resource.");
            }
            foreach ($curlHandlers as $ch) {
                if (curl_multi_add_handle($mh, $ch) !== 0) {
                    throw new Exception("Error adding a normal cURL handle to a cURL multi handle.");
                }
            }
            $running = null;
            curl_multi_exec($mh, $running);
            do {// The default is to wait one second.
                curl_multi_select($mh);
                curl_multi_exec($mh, $running);
            } while($running > 0);
            $ans = array();
            foreach($curlHandlers as $key => $ch) {
                $ans[$key] = self::processCurlAnswer($ch, curl_multi_getcontent($ch));
                curl_multi_remove_handle($mh, $ch);
                curl_close($ch);
            }
            curl_multi_close($mh);
            return $ans;
        } catch (Exception $e) {
            error_log($e->getMessage());
            $ans = array();
            foreach($curlHandlers as $key => $ch) {
                $ans[$key] = false;
            }
            return $ans;
        }
    }

    private static function processCurlAnswer($ch, $content) {
        $errorCode = curl_errno($ch);
        $errorMessage = curl_error($ch);
        if ($errorCode) {
            error_log("Curl error " . $errorCode . ": " . $errorMessage);
            return false;
        } else {
            $response = array(
                'code' => curl_getinfo($ch, CURLINFO_HTTP_CODE),
                'body' => $content,
            );
            return $response;
        }
    }
}
