<?php

	//check if the parameter $pname was passed by POST or GET and set default if there's not
	public static function getParameter($pname, $default=NULL){
        if(isset($_GET[$pname])){
            return $_GET[$pname];
        }else if (isset($_POST[$pname])) {
            return $_POST[$pname];
        }
        return $default;
    }


    //send json data using POST to $url
	public static function sendUrlPostJson($url, $data, $timeoutMs = 30000, $contentType = "application/json"){
	        //get current Unix timestamp. setting param to "true" returns a float instead of a string
	        $time_start = microtime(true);
	        
	        //logging using Yii
	        Yii::log("CURL url= : $url" . "  " . $data, CLogger::LEVEL_INFO);

        	// init a cURL session- returns a a cURL handle
	        $ch = curl_init($url);


	        curl_setopt($ch, CURLOPT_TIMEOUT_MS, $timeoutMs);
	        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
	        
        	// followLocation is set true to follow any "Location:" header sent by server as part of the HTTP header 
        	// (this is recursive and can keep following many headers unless CURLOPT_MAXREDIRS is set)
	        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	        
	        curl_setopt($ch, CURLOPT_POSTFIELDS,$data);
	        
        	// returnTransfer is set to true to return the transfer as a string of the return value of curl_exec() instead of outputting out directly
	        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

	        // an array of HTTP header fields to set. Must follow below format
	        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
	                "Content-Type: $contentType",
	                'Content-Length: ' . strlen($data))
	        );

	        // curlopt_header is set to true to include header in the output
	        curl_setopt($ch, CURLOPT_HEADER, true);

        	// execute the bastard 
	        $response = curl_exec ( $ch );

	        // error number and error message
	        $err = curl_errno ( $ch );
	        $errmsg = curl_error ( $ch );
	        
	        // get the last received HTTP code
	        $httpCode = curl_getinfo ( $ch, CURLINFO_HTTP_CODE );

	        // get total size of received header
	        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
	        // function to get headers (can be found below)
	        $header = self::get_headers_from_curl_response($response);

	        
            $content = substr($response, $header_size);
	        
	        if(!$content){
	            if($err==0){
	                $content = "{\"status\": 0}";
	            }else{
	                $content = "{\"status\": 1, \"msg\": \"$errmsg\"}";
	            }
	        }

	        //remember to close curl request
	        curl_close($ch);


	        $time_end = microtime(true);
	        $time_cost = $time_end - $time_start;
         	if(isset($GLOBALS['dev']) && $GLOBALS['dev']){
	            Yii::log("CURL RESPONSE $content");
	        }
	        Yii::log("CURL END , cost time: $time_cost", CLogger::LEVEL_INFO);
	        return $content;
	    }


	    private static function get_headers_from_curl_response($response){
        $headers = array();
        $header_text = substr($response, 0, strpos($response, "\r\n\r\n"));

        foreach (explode("\r\n", $header_text) as $i => $line)
            if ($i === 0)
                $headers['http_code'] = $line;
            else
            {
                list ($key, $value) = explode(': ', $line);

                $headers[$key] = $value;
            }

        return $headers;
    	}

		//upload file using POST
	    public static function sendUrlPostJsonUpload($url, $data){

	        $ch = curl_init();
	        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	        curl_setopt($ch, CURLOPT_URL, $url);
	        curl_setopt($ch, CURLOPT_POST, 1);


	        // remember to use file_get_contents(data)
	        // alternatively can check out CURLFile class
	        curl_setopt($ch, CURLOPT_POSTFIELDS,file_get_contents($data));
	        $response = curl_exec($ch);

	        curl_close($ch);
	        return $response;
	    }

    	//curl using GET
		public static function sendUrlGet($url, $timeoutMs = 10000) {
	        $time_start = microtime(true);
	        Yii::log("CURL url= : $url", CLogger::LEVEL_INFO);
	        
	        $ch = curl_init($url);
	        curl_setopt($ch, CURLOPT_TIMEOUT_MS, $timeoutMs);
	        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	        $result = curl_exec($ch);
	        curl_close($ch);
	        
	        $time_end = microtime(true);
	        $time_cost = $time_end - $time_start;
	        Yii::log("CURL END , cost time: $time_cost", CLogger::LEVEL_INFO);
	        return $result;
    	}


    	//create URL with http or https depending on the requirement. $path can be relative path, $param array to be passed by GET
		public static function createUrl($path, $params=array()){
	        if(is_array($path) && sizeof($path)>0){
	            $path = $path[0];
	        }
	        if(isset($GLOBALS['MOD']) && $GLOBALS['MOD']!='prd'){
	            return Yii::app()->createUrl($path, $params);
	        }else{
	            return Yii::app()->createAbsoluteUrl($path, $params, "https");
	        }
	    }

?> 