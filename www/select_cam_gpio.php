<!DOCTYPE html>
<?php
   define('BASE_DIR', dirname(__FILE__));
   require_once(BASE_DIR.'/config.php');
   $config = array();
   $debugString = "";
   $macros = array('error_soft','error_hard','start_img','end_img','start_vid','end_vid','end_box','do_cmd','motion_event','startstop');
   $options_mm = array('Average' => 'average', 'Spot' => 'spot', 'Backlit' => 'backlit', 'Matrix' => 'matrix');
   $options_em = array('Off' => 'off', 'Auto' => 'auto', 'Night' => 'night', 'Nightpreview' => 'nightpreview', 'Backlight' => 'backlight', 'Spotlight' => 'spotlight', 'Sports' => 'sports', 'Snow' => 'snow', 'Beach' => 'beach', 'Verylong' => 'verylong', 'Fixedfps' => 'fixedfps');
   $options_wb = array('Off' => 'off', 'Auto' => 'auto', 'Sun' => 'sun', 'Cloudy' => 'cloudy', 'Shade' => 'shade', 'Tungsten' => 'tungsten', 'Fluorescent' => 'fluorescent', 'Incandescent' => 'incandescent', 'Flash' => 'flash', 'Horizon' => 'horizon');
//   $options_ie = array('None' => 'none', 'Negative' => 'negative', 'Solarise' => 'solarise', 'Sketch' => 'sketch', 'Denoise' => 'denoise', 'Emboss' => 'emboss', 'Oilpaint' => 'oilpaint', 'Hatch' => 'hatch', 'Gpen' => 'gpen', 'Pastel' => 'pastel', 'Watercolour' => 'watercolour', 'Film' => 'film', 'Blur' => 'blur', 'Saturation' => 'saturation', 'Colourswap' => 'colourswap', 'Washedout' => 'washedout', 'Posterise' => 'posterise', 'Colourpoint' => 'colourpoint', 'ColourBalance' => 'colourbalance', 'Cartoon' => 'cartoon');
// Remove Colourpoint and colourbalance as they kill the camera
   $options_ie = array('None' => 'none', 'Negative' => 'negative', 'Solarise' => 'solarise', 'Sketch' => 'sketch', 'Denoise' => 'denoise', 'Emboss' => 'emboss', 'Oilpaint' => 'oilpaint', 'Hatch' => 'hatch', 'Gpen' => 'gpen', 'Pastel' => 'pastel', 'Watercolour' => 'watercolour', 'Film' => 'film', 'Blur' => 'blur', 'Saturation' => 'saturation', 'Colourswap' => 'colourswap', 'Washedout' => 'washedout', 'Posterise' => 'posterise', 'Cartoon' => 'cartoon');
   $options_ce_en = array('Disabled' => '0', 'Enabled' => '1');
   $options_ro = array('No rotate' => '0', 'Rotate_90' => '90', 'Rotate_180' => '180', 'Rotate_270' => '270');
   $options_fl = array('None' => '0', 'Horizontal' => '1', 'Vertical' => '2', 'Both' => '3');
   $options_bo = array('Off' => '0', 'Background' => '2');
   $options_av = array('V2' => '2', 'V3' => '3');
   $options_at_en = array('Disabled' => '0', 'Enabled' => '1');
   $options_ac_en = array('Disabled' => '0', 'Enabled' => '1');
   $options_ab = array('Off' => '0', 'On' => '1');
   $options_vs = array('Off' => '0', 'On' => '1');
   $options_rl = array('Off' => '0', 'On' => '1');
   $options_vp = array('Off' => '0', 'On' => '1');
   $options_mx = array('Internal' => '0', 'External' => '1', 'Monitor' => '2');
   $options_mf = array('Off' => '0', 'On' => '1');
   $options_cn = array('First' => '1', 'Second' => '2');
   $options_st = array('Off' => '0', 'On' => '1');
   

   function user_buttons() {
      $buttonString = "";
	  $buttonCount = 0;
      if (file_exists("userbuttons")) {
		$lines = array();
		$data = file_get_contents("userbuttons");
		$lines = explode("\n", $data);
		foreach($lines as $line) {
			if (strlen($line) && (substr($line, 0, 1) != '#') && buttonCount < 6) {
				$index = strpos($line, ',');
				if ($index !== false) {
					$buttonName = substr($line, 0, $index);
					$macroName = substr($line, $index +1);
					$buttonString .= '<input id="' . $buttonName . '" type="button" value="' . $buttonName . '" onclick="send_cmd(' . "'sy " . $macroName . "'" . ')" class="btn btn-primary" >' . "\r\n";
					$buttonCount += 1;
				}
			}
		}
      }
	  if (strlen($buttonString)) {
		  echo '<div class="container-fluid text-center">' . $buttonString . "</div>\r\n";
	  }
   }

   
   function getExtraStyles() {
      $files = scandir('css');
      foreach($files as $file) {
         if(substr($file,0,3) == 'es_') {
            echo "<option value='$file'>" . substr($file,3, -4) . '</option>';
         }
      }
   }
   
  
   function makeOptions($options, $selKey) {
      global $config;
      switch ($selKey) {
         case 'flip': 
            $cvalue = (($config['vflip'] == 'true') || ($config['vflip'] == 1) ? 2:0);
            $cvalue += (($config['hflip'] == 'true') || ($config['hflip'] == 1) ? 1:0);
            break;
         case 'MP4Box': 
            $cvalue = $config[$selKey];
            if ($cvalue == 'background') $cvalue = 2;
            break;
         default: $cvalue = $config[$selKey]; break;
      }
      if ($cvalue == 'false') $cvalue = 0;
      else if ($cvalue == 'true') $cvalue = 1;
      foreach($options as $name => $value) {
         if ($cvalue != $value) {
            $selected = '';
         } else {
            $selected = ' selected';
         }
         echo "<option value='$value'$selected>$name</option>";
      }
   }

   function makeInput($id, $size, $selKey='') {
      global $config, $debugString;
      if ($selKey == '') $selKey = $id;
      switch ($selKey) {
         case 'tl_interval': 
            if (array_key_exists($selKey, $config)) {
               $value = $config[$selKey] / 10;
            } else {
               $value = 3;
            }
            break;
         case 'watchdog_interval':
            if (array_key_exists($selKey, $config)) {
               $value = $config[$selKey] / 10;
            } else {
               $value = 0;
            }
            break;
         default: $value = $config[$selKey]; break;
      }
      echo "<input type='text' size=$size id='$id' value='$value'>";
   }
   
   function macroUpdates() {
      global $config, $debugString, $macros;
	  $m = 0;
	  $mTable = '';
	  foreach($macros as $macro) {
		  $value = $config[$macro];
		  if(substr($value,0,1) == '-') {
			  $checked = '';
			  $value = substr($value,1);
		  } else {
			  $checked = 'checked';
		  }
		  $mTable .= "<TR><TD>Macro:$macro</TD><TD><input type='text' size=16 id='$macro' value='$value'>\r\n";
		  $mTable .= "<input type='checkbox' $checked id='$macro" . "_chk'>\r\n";
		  $mTable .= "<input type='button' value='OK' onclick=" . '"send_macroUpdate' . "($m,'$macro')\r\n" . ';"></TD></TR>';
		  $m++;
	  }
      echo $mTable;
   }

   function getImgWidth() {
      global $config;
      if($config['vector_preview'])
         return 'style="width:' . $config['width'] . 'px;"';
      else
         return '';
   }
   
   function getLoadClass() {
      global $config;
      if(array_key_exists('fullscreen', $config) && $config['fullscreen'] == 1)
         return 'class="fullscreen" ';
      else
         return '';
   }

   function simple_button() {
	   global $toggleButton, $userLevel;
	   if ($toggleButton != "Off" && $userLevel > USERLEVEL_MIN) {
		  echo '<input id="toggle_display" type="button" class="btn btn-primary" value="' . $toggleButton . '" style="position:absolute;top:60px;right:10px;" onclick="set_display(this.value);">';
	   }
   }

   if (isset($_POST['extrastyle'])) {
      if (file_exists('css/' . $_POST['extrastyle'])) {
         $fp = fopen(BASE_DIR . '/css/extrastyle.txt', "w");
         fwrite($fp, $_POST['extrastyle']);
         fclose($fp);
      }
   }

   function getDisplayStyle($context, $userLevel) {
	    global $Simple;
	    if ($Simple == 1) {
			echo 'style="display:none;"';
		} else {
			switch($context) {
				case 'navbar':
					if ($userLevel == USERLEVEL_MIN)
						echo 'style="display:none;"';
					break;
				case 'actions':
					if ($userLevel == USERLEVEL_MIN)
						echo 'style="display:none;"';
					break;
				case 'settings':
					if ((int)$userLevel != USERLEVEL_MAX)
						echo 'style="display:none;"';
					break;
			}
		}
   }

   $toggleButton = "Off";
   $Simple = 0;
   $allowSimple = "SimpleOn";
   if(isset($_COOKIE["display_mode"])) {
      if($_COOKIE["display_mode"] == "Full") {
		 $allowSimple = "SimpleOff";
         $toggleButton = "Simple";
         $Simple = 2;
      } else if($_COOKIE["display_mode"] == "Simple") {
		 $allowSimple = "SimpleOff";
         $toggleButton = "Full";
         $Simple = 1;
      } else {
		 $allowSimple = "SimpleOn";
         $toggleButton = "Off";
         $Simple = 0;
	  }
   }
  
   $streamButton = "MJPEG-Stream";
   $mjpegmode = 0;
   if(isset($_COOKIE["stream_mode"])) {
      if($_COOKIE["stream_mode"] == "MJPEG-Stream") {
         $streamButton = "Default-Stream";
         $mjpegmode = 1;
      }
   }
   $config = readConfig($config, CONFIG_FILE1);
   $config = readConfig($config, CONFIG_FILE2);
   $video_fps = $config['video_fps'];
   $divider = $config['divider'];
   $serverSoftware = $_SERVER['SERVER_SOFTWARE'];
   if(stripos($serverSoftware, 'apache') !== false) {
	   $user = apache_getenv("REMOTE_USER"); 
   } else if(stripos($serverSoftware, 'nginx') !== false) {
	   try {
		   $user = $remote_user;
	   } catch  (Exception $e) {
		$user = '';
	   }
   } else {
	   $user = '';
   }
   writeLog("Logged in user:" . $user . ":");
   $userLevel =  getUserLevel($user);
   writeLog("UserLevel " . $userLevel);
  ?>

<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
      <title>AniHome Record</title>
      <link rel="stylesheet" href="css/style_minified.css" />
      <!--link rel="stylesheet" href="style_new/style.css" /-->
      <link rel="stylesheet" href="<?php echo getStyle(); ?>" />
      <script src="js/style_minified.js"></script>
      <script src="js/script.js"></script>
      <!--link rel="stylesheet" href="camgpio.css"-->
      <script src="js/camgpio.js"></script>
</head>
<body onload="load()">

<nav class="navbar navbar-inverse navbar-fixed-top">
  <div class="container-fluid">
    <div class="navbar-header">
      <a class="navbar-brand" href="index.php">AniHome</a>
    </div>
    <ul class="nav navbar-nav">
      <li><a href="index.php">Home</a></li>
      <li><a href="select_cam_settings.php">Settings</a></li>
      <li><a href="select_cam_preview.php">Preview</a></li>
      <li class="active"><a href="select_cam_gpio.php">GPIO Control</a></li>
    </ul>
  </div>
</nav>

      <div class="container-fluid liveimage">
         <div><img id="mjpeg_dest" <?php echo getLoadClass() . getImgWidth();?> onclick="toggle_fullscreen(this);" src="./loading.jpg"></div>
         <div id="main-buttons" style="position: absolute; top:70px; left:35%; width : 600px; height: 80%;">
	<button type="button" disabled style="width:150px;" class="btn btn-warning">3V3 Power</button>
	<button type="button" disabled style="width:150px;" class="btn btn-danger">5V Power</button><br><br>
	<button type="button" style="width:150px;" class="btn btn-success" value="2" onclick="tog(this.value)" >GPIO 02</button>
	<button type="button" disabled style="width:150px;" class="btn btn-danger">5V Power</button><br><br>
	<button type="button" style="width:150px;" class="btn btn-success" value="3" onclick="tog(this.value)" >GPIO 03</button>
	<button type="button" disabled style="width:150px;" class="btn">Ground</button><br><br>
	<button type="button" style="width:150px;" class="btn btn-success" value="4" onclick="tog(this.value)" >GPIO 04</button>
	<button type="button" style="width:150px;" class="btn btn-success" value="14" onclick="tog(this.value)">GPIO 14</button><br><br>
	<button type="button" disabled style="width:150px;" class="btn">Ground</button>
	<button type="button" style="width:150px;" class="btn btn-success" value="15" onclick="tog(this.value)" >GPIO 15</button><br><br>
	<button type="button" style="width:150px;" class="btn btn-success" value="17" onclick="tog(this.value)" >GPIO 17</button>
	<button type="button" style="width:150px;" class="btn btn-success" value="18" onclick="tog(this.value)" >GPIO 18</button><br><br>
	<button type="button" style="width:150px;" class="btn btn-success" value="27" onclick="tog(this.value)" >GPIO 27</button>
	<button type="button" disabled style="width:150px;" class="btn">Ground</button><br><br>
	<button type="button" style="width:150px;" class="btn btn-success" value="22" onclick="tog(this.value)" >GPIO 22</button>
	<button type="button" style="width:150px;" class="btn btn-success" value="23" onclick="tog(this.value)" >GPIO 23</button><br><br>
	<button type="button" disabled style="width:150px;" class="btn btn-warning">3V3 Power</button>
	<button type="button" style="width:150px;" class="btn btn-success" value="24" onclick="tog(this.value)" >GPIO 24</button><br><br>
	<button type="button" style="width:150px;" class="btn btn-success" value="10" onclick="tog(this.value)" >GPIO 10</button>
	<button type="button" disabled style="width:150px;" class="btn">Ground</button><br><br>
	<button type="button" style="width:150px;" class="btn btn-success" value="9" onclick="tog(this.value)" >GPIO 09</button>
	<button type="button" style="width:150px;" class="btn btn-success" value="25" onclick="tog(this.value)" >GPIO 25</button><br><br>
	<button type="button" style="width:150px;" class="btn btn-success" value="11" onclick="tog(this.value)" >GPIO 11</button>
	<button type="button" style="width:150px;" class="btn btn-success" value="8" onclick="tog(this.value)" >GPIO 08</button><br><br>
	<button type="button" disabled style="width:150px;" class="btn">Ground</button>
	<button type="button" style="width:150px;" class="btn btn-success" value="7" onclick="tog(this.value)" >GPIO 07</button><br><br>
	<button type="button" disabled style="width:150px;" class="btn btn-info">ID_SD</button>
	<button type="button" disabled style="width:150px;" class="btn btn-info">ID_SC</button><br><br>
	<button type="button" style="width:150px;" class="btn btn-success" value="5" onclick="tog(this.value)" >GPIO 05</button>
	<button type="button" disabled style="width:150px;" class="btn">Ground</button><br><br>
	<button type="button" style="width:150px;" class="btn btn-success" value="6" onclick="tog(this.value)" >GPIO 06</button>
	<button type="button" style="width:150px;" class="btn btn-success" value="12" onclick="tog(this.value)" >GPIO 12</button><br><br>
	<button type="button" style="width:150px;" class="btn btn-success" value="13" onclick="tog(this.value)" >GPIO 13</button>
	<button type="button" disabled style="width:150px;" class="btn">Ground</button><br><br>
	<button type="button" style="width:150px;" class="btn btn-success" value="19" onclick="tog(this.value)" >GPIO 19</button>
	<button type="button" style="width:150px;" class="btn btn-success" value="16" onclick="tog(this.value)" >GPIO 16</button><br><br>
	<button type="button" style="width:150px;" class="btn btn-success" value="26" onclick="tog(this.value)" >GPIO 26</button>
	<button type="button" style="width:150px;" class="btn btn-success" value="20" onclick="tog(this.value)" >GPIO 20</button><br><br>
	<button type="button" disabled style="width:150px;" class="btn">Ground</button>
	<button type="button" style="width:150px;" class="btn btn-success" value="21" onclick="tog(this.value)" >GPIO 21</button>	
         </div>
<br><br>
      </div>



</body>
</html>
