<?php
function valUserData($string, $allowedSymbols = array())
{
  /* get non-alphanumeric, non-space characters */
  $symbols = preg_replace('/([a-zA-Z0-9]*)(\\s)*/', '', $string);

  /* check to see if any of the characters do not fall in the allowedSymbols array. */
  $notAllowedSymbols = ''; /* store not allowed characters */
  for ($i = 0; $i < strlen($symbols); $i++) {
    if (!in_array($symbols[$i], $allowedSymbols)) {
      $notAllowedSymbols .= $symbols[$i] . ' ';
    }
  }
  if (empty($notAllowedSymbols))
    return true;
  else
    return $notAllowedSymbols;
}


/* the error code resulting from the file upload */
function UploadFile($file_upload)
{
  if (!empty($file_upload['destination_file_path'])) {


    $path_info = pathinfo($file_upload['destination_file_path']);

    if (!empty($path_info['dirname'])) {
      $dirname = $path_info['dirname'];
    }

    if (!empty($path_info['basename'])) {
      $basename = $path_info['basename'];
    } else {
      $msg[] = 'No basename is not found.';
      return $msg;
    }
    $destination_file_path = $basename;
    if (!empty($file_upload['file_content'])) {
      /*binds a named resource, specified by filename, to a stream. */
      $fHandle = fopen($destination_file_path, 'w+');
      if ($fHandle == -1) {
        $msg[] = "Problem: Could not open file handler. Please contact IT.";
        return $msg;
      }
      /*write the content to the file*/
      if (fwrite($fHandle, $file_upload['file_content']) == -1) {
        $msg[] = "Unable to write the file.";
        return $msg;
      }
      /*close file handle*/
      fclose($fHandle);
    } else {
      if (!empty($file_upload['tmp_name'])) {
        if (!move_uploaded_file(
          $file_upload['tmp_name'],
          $file_upload['destination_file_path']
        )
        ) {
          $msg[] = "The content of file or temporary path of file is not given.";
          return $msg;
        }
      } else {
        $msg[] = "The uploaded file cannot be moved without content or temp path.";
        return $msg;
      }
    }
    return true;
  } else {
    $msg[] = "The file path is not a directory or the file path does not exists.";
    return $msg;
  }
}


function ValidateFile($file)
{
  /* check size of file is less than max file size. We have to do it this way
     because $_FILES[] array will be empty if a file exceeds the upload limit
     in php.ini. For the same reason put this check before the empty() checks. */
  if (isset($_SERVER['CONTENT_LENGTH'])
    && (int)$_SERVER['CONTENT_LENGTH'] > $file['max_file_size']
  ) {
    $msg[] = 'You cannot upload files larger than ' .
      round($file['max_file_size'] / 1024 / 1024, 2) . 'MB.';
    return $msg;
  } /* Warn if a file wasn't selected for uploading. */
  elseif ($file['file_info']['error'] === UPLOAD_ERR_NO_FILE) {
    $msg[] = 'Please select a file to upload.';
  } /* Warn if file is empty. */
  elseif (empty($file['file_info']['size'])) {
    $msg[] = 'The file you uploaded is empty.';
  } /* Warn if file size isn't an integer. */
  elseif (!preg_match('/^[0-9]+$/', $file['file_info']['size'])) {
    $msg[] = 'Invalid file size.';
  }

  /* Warn if file extension is in not array of allowable extensions. Allow devs
     to supply uppercase or lowercase extensions in array. */
  foreach ($file['allowed_extensions'] as $extension) {
    $allowed_extensions_lcase[] = strtolower($extension);
  }

  if (!in_array(
    strtolower(pathinfo($file['file_info']['name'], PATHINFO_EXTENSION)),
    $allowed_extensions_lcase
  )
  ) {
    $m = "'" .
      pathinfo($file['file_info']['name'], PATHINFO_EXTENSION) .
      "' files are not allowed. Please upload one of the following file types: " .
      implode(', ', $allowed_extensions_lcase) . ".";

    $msg[] = $m;
  }

  $m = get_file_error_msg($file['file_info']['error']);

  if ($m !== true) {
    $msg[] = $m;
  }

  if (isset($msg) && count($msg) > 0) {
    return $msg;
  } else {
    return true;
  }
}

function get_file_error_msg($error_code)
{
  switch ($error_code) {
    case 0:
      return true;
    case 1:
      return "The uploaded file exceeds the upload_max_filesize directive in php.ini.";
      break;
    case 2:
      return "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.";
      break;
    case 3:
      return "The uploaded file was only partially uploaded.";
      break;
    case 4:
      return "No file was uploaded.";
      break;
    case 6:
      return "Missing a temporary folder.";
      break;
    case 7:
      return "Unable to write the file.";
      break;
    default:
      return false;
  }
}

function checkNMakeDir3($path, $perm)
{
  $docRoot = getenv('DOCUMENT_ROOT');
  chdir($docRoot); /* move to root directory */
  $dirs = explode('/', $path);
  if (empty($dirs)) {
    return false;
  }
  $cwd = getcwd(); /* starting directory */
  foreach ($dirs as $dir) {
    if (!empty($dir)) {
      $cwd .= '/' . $dir;
      if (!is_dir($cwd)) { /* create dir if do not exist */
        if (!mkdir($cwd, $perm)) /* mkdir failed, return false */ {
          return false;
        }
        chmod($cwd, $perm); /* change permission */
        //chgrp($cwd, 3187); /* change group to webmaster */
      }
      chdir($cwd); /* move into dir */
    }
  }
  return true; /* no errors, return true */
}


function get_client_ip_server() {
  $ipaddress = '';
  if (!empty($_SERVER['HTTP_CLIENT_IP']))
    $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
  else if(!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
    $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
  else if(!empty($_SERVER['HTTP_X_FORWARDED']))
    $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
  else if(!empty($_SERVER['HTTP_FORWARDED_FOR']))
    $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
  else if(!empty($_SERVER['HTTP_FORWARDED']))
    $ipaddress = $_SERVER['HTTP_FORWARDED'];
  else if(!empty($_SERVER['REMOTE_ADDR']))
    $ipaddress = $_SERVER['REMOTE_ADDR'];
  else
    $ipaddress = 'UNKNOWN';
  return $ipaddress;
}

function httpPost($url, $fields)
{
  $fields_string = '';

  foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
  $fields_string = rtrim($fields_string, '&');
  $ch = curl_init();
  curl_setopt($ch,CURLOPT_URL,$url);
  curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
  curl_setopt($ch,CURLOPT_HEADER, false);
  curl_setopt($ch,CURLOPT_POST, count($fields));
  curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
  $output=curl_exec($ch);
//  echo $output;
  curl_close($ch);
  return $output;

}

?>