<?php
include_once ('config.php');

class Crud extends Dbconnect 
{   
    public $cn, $db, $db_host, $db_user, $db_pass, $db_name;
    
    public function __construct() 
	{ 
	parent::__construct();        
	} 

    public function mf_query($query) // EXECUTE USER QUERY
	{
		$_SESSION['last_query'] = $query;
		if ($query) {
			return mysqli_query($GLOBALS['myCon'], $query);
		} else {
			return '';
		}
	}

    public function mf_multi_query($query) // EXECUTE USER QUERY
	{
		$_SESSION['last_query'] = $query;
		return mysqli_multi_query($GLOBALS['myCon'], $query);
	}

    public function mf_field_count() // EXECUTE USER QUERY
	{
		return mysqli_field_count($GLOBALS['myCon']);
	}

    public function mf_last_query() // EXECUTE USER QUERY
	{
		return $_SESSION['last_query'];
	}

    public function mf_error() // EXECUTE USER QUERY
	{
		return mysqli_error($GLOBALS['myCon']);
	}

    public function mf_getValue($table, $field, $where, $condition) // FUNCTION TO SELECT RECORD IN SPECIFIED TABLE
	{
		$qry = "SELECT $field from $table where $where='$condition' LIMIT 1";
		// echo $qry;
		$result = $this->mf_query($qry);
		if ($this->mf_affected_rows() > 0) {
			$row = $this->mf_fetch_array($result);
			return stripslashes($row[$field]);
		} else {
			return "";
		}
	}

    public function mf_getValue_Qry($table, $field, $where) // FUNCTION TO SELECT RECORD IN SPECIFIED TABLE
	{
		$qry = "SELECT $field from $table $where LIMIT 1";
		// echo $qry.'<br>';
		$result = $this->mf_query($qry);
		if ($this->mf_affected_rows() > 0) {
			$row = $this->mf_fetch_array($result);

			return stripslashes($row[$field]);
		} else {
			return "";
		}
	}

    public function is_valueexist($qry) // FUNCTION TO SELECT RECORD IN SPECIFIED TABLE
	{
		$result = $this->mf_query($qry);
		if ($this->mf_affected_rows() > 0) {
			$row = $this->mf_fetch_array($result);
			return stripslashes($row['id']);
		} else {
			return "";
		}
	}

    public function mf_fetch_array($result) // RETURN SINGLE ROW IN ARRAY FORM
	{
		if ($result) {
			return mysqli_fetch_array($result, MYSQLI_ASSOC);
		} else {
			return array();
		}
	}

    public function mf_affected_rows() // RETURN TOTAL AFFECTED ROW WHILE QUERY EXECUTED
	{
		return mysqli_affected_rows($GLOBALS['myCon']);
	}   

    public function mf_getMultiValue($table, $field, $where, $condition) // FUNCTION TO SELECT RECORD IN SPECIFIED TABLE
	{
		$fldlist = "";
		if (is_array($field)) {
			foreach ($field as $k => $v) {
				if ($fldlist == "") {
					$fldlist .= $v;
				} else {
					$fldlist .= "," . $v;
				}
			}
		}
		$rval = array();
		$qry = "SELECT $fldlist from $table where $where='$condition'";
		// echo $qry;
		$result = $this->mf_query($qry);
		if ($this->mf_affected_rows() > 0) {
			$row = $this->mf_fetch_array($result);
			foreach ($field as $k => $v) {
				$rval[] = stripslashes($row[$v]);
			}
		}
		return $rval;
	}
	public function mf_getMultiValueNew($table, $field, $where, $condition) // FUNCTION TO SELECT RECORD IN SPECIFIED TABLE
	{
		$fldlist = "";
		if (is_array($field)) {
			foreach ($field as $k => $v) {
				if ($fldlist == "") {
					$fldlist .= $v;
				} else {
					$fldlist .= "," . $v;
				}
			}
		}
		$rval = array();
		$qry = "SELECT $fldlist from $table where $where='$condition'";
		//echo $qry;
		$result = $this->mf_query($qry);
		if ($this->mf_affected_rows() > 0) {
			$row = $this->mf_fetch_array($result);
		}
		return $row;
	}
	public function mf_getMultiValue_Qry($table, $field, $where) // FUNCTION TO SELECT RECORD IN SPECIFIED TABLE
	{
		$fldlist = "";
		if (is_array($field)) {
			foreach ($field as $k => $v) {
				if ($fldlist == "") {
					$fldlist .= $v;
				} else {
					$fldlist .= "," . $v;
				}
			}
		}
		$rval = array();
		$qry = "SELECT $fldlist from $table $where ";
		// echo $qry.'<br/>';
		$result = $this->mf_query($qry);
		if ($this->mf_affected_rows() > 0) {
			$row = $this->mf_fetch_array($result);
			foreach ($field as $k => $v) {
				$rval[] = stripslashes($row[$v]);
			}
		}
		return $rval;
	}

    public function mf_dbinsert($table, $data) // FUNCTION TO INSERT NEW RECORD IN SPECIFIED TABLE
	{
		$qry = "INSERT INTO " . $table . " set ";
		foreach ($data as $fld => $val) {
			$qry .= $fld . "='" . $this->add_security($val) . "',";
		}
		$qry = substr($qry, 0, -1);
		// echo $qry; exit;
		return $this->mf_query($qry);
	}

    public function add_security($val) // RETURN VALUE WITH SECURITY
	{
		return mysqli_real_escape_string($GLOBALS['myCon'], $val);
	}

	public function mf_dbinsert_id()
	{
		return mysqli_insert_id($GLOBALS['myCon']);
	}

    public function mf_dbselect($table, $fields, $conditions, $orderby = "") // FUNCTION TO SELECT RECORD IN SPECIFIED TABLE
	{
		$qry = "SELECT ";
		foreach ($fields as $fld) {
			$qry .= $fld . ",";
		}
		$qry = substr($qry, 0, -1);
		$qry .= " where 1=1 ";
		if (is_array($conditions)) {
			foreach ($conditions as $fld => $val) {
				$qry .= " and $fld='$val' ";
			}
		}
		if ($orderby != "") {
			$qry .= " order by $orderby ";
		}
		$result = $this->mf_query($qry);
		return $result;
	}

    public function mf_dbupdate($table, $data, $whare) // FUNCTION TO UPDATE TABLE DATA
	{

		$qry = "UPDATE " . $table . " set ";
		foreach ($data as $fld => $val) {
			$qry .= $fld . "='" . $this->add_security($val) . "',";
		}
		$qry = substr($qry, 0, -1);
		$qry .= " " . $whare;
		// echo '<pre>'.$qry.'<br/>'; exit();
		return $this->mf_query($qry);
	}

    public function mf_dbdelete($table, $fld, $val) // FUNCTION TO DELETE TABLE ROW
	{
		$qry = "DELETE FROM " . $table . " where " . $fld . "='" . $val . "'";
		return $this->mf_query($qry);
	}

    public function rand_string($length)
	{
		$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
		$size = strlen($chars);
		for ($i = 0; $i < $length; $i++) {
			$str .= $chars[rand(0, $size - 1)];
		}
		return $str;
	}

    public function randfilename($id)
	{
		return uniqid() . $id;
	}

    public function createFileName($id)
	{
		return uniqid() . $id;
	}

    public function mf_createcombo($query, $opt_value, $disp_value, $selected = "", $firstval = "-Select-")
	{
		// echo $query.'<br/>';
		if ($firstval != "") {
			$cmbtext = "<option value=''>$firstval</option>";
		}
		$result = $this->mf_query($query);
		if ($this->mf_affected_rows() > 0) {
			while ($row = $this->mf_fetch_array($result)) {
				$sel = "";
				if (stripslashes($row[$opt_value]) == stripslashes($selected)) {
					$sel = "selected='selected'";
				}
				$cmbtext .= "<option value='" . $row[$opt_value] . "' $sel>" . stripslashes($row[$disp_value]) . "</option>";
			}
		}
		//echo $cmbtext;
		return $cmbtext;
	}

    public function delUploadFile($table, $field, $where, $condition, $path = "")
	{
		$qry = "SELECT $field from $table where $where='$condition'";
		$result = $this->mf_query($qry);
		if ($this->mf_affected_rows() > 0) {
			$row = $this->mf_fetch_array($result);
			$vid = stripslashes($row[$field]);
			if (is_file($path . $vid)) {
				unlink($path . $vid);
			}
		}
	}

    public function creatDir($fixPath)
	{
		$year = date("y");
		$month = date("m");
		$directory = "$year/$month/";
		if (!is_dir($fixPath . $directory)) {
			mkdir($fixPath . $directory, 755, true);
		}
		return $directory;
	}

    public function checkUploadFileExt($ext, $allowedExt)
	{
		$ret = true;
		if ($allowedExt != '') {
			$ret = false;
			$alext = explode('|', $allowedExt);
			foreach ($alext as $val) {
				if (strtolower($val) == strtolower($ext)) {
					$ret = true;
				}
			}
		}
		return $ret;
	}

    public function uploadFile($tmpPath, $fileName, $fixPath)
	{
		$year = date("y");
		$month = date("m");
		$directory = "$year/$month/";
		$fullPath = $fixPath . $directory . $fileName;
		if (!is_dir($fixPath . $directory)) {
			mkdir($fixPath . $directory, 755, true);
		}
		if (move_uploaded_file($tmpPath, $fullPath)) {
			return $directory . $fileName;
		}
	}

    public function curTimedate()
	{
		date_default_timezone_set('Asia/Kolkata');
		return date("Y-m-d H:i:s");
	}
	public function curDate()
	{
		date_default_timezone_set('Asia/Kolkata');
		return date("Y-m-d");
	}
	public function curTime()
	{
		date_default_timezone_set('Asia/Kolkata');
		return date("H:i:s");
	}
	public function curDateFormat()
	{
		date_default_timezone_set('Asia/Kolkata');
		return date("d/m/Y");
	}
	public function dispTZTimedate($dtval)
	{
		if ($dtval != '0000-00-00 00:00:00' && $dtval != '') {
			return date("d/m/Y h:i A", strtotime($dtval));
		} else {
			return '';
		}
	}
	public function saveTZTimedate($dtval = '')
	{
		if ($dtval != '') {
			$dDateArr = explode(' ', $dtval);
			$dDate = $this->date2savenew($dDateArr[0]);
			$dTime = $this->timetosave($dDateArr[1] . ' ' . $dDateArr[2]);
			return $dDate . ' ' . $dTime;
		} else {
			return '';
		}
	}
	public function dispTZdate($dtval)
	{
		if ($dtval != '0000-00-00') {
			return date("d/m/Y", strtotime($dtval));
		}
	}


    public function convert_number_to_words($number)
	{
		$no = round((float)$number);
		$point = round((float)$number - $no, 2) * 100;
		$hundred = null;
		$digits_1 = strlen($no);
		$i = 0;
		$str = array();
		$words = array(
			'0' => '',
			'1' => 'One',
			'2' => 'Two',
			'3' => 'Three',
			'4' => 'Four',
			'5' => 'Five',
			'6' => 'Six',
			'7' => 'Seven',
			'8' => 'Eight',
			'9' => 'Nine',
			'10' => 'Ten',
			'11' => 'Eleven',
			'12' => 'Twelve',
			'13' => 'Thirteen',
			'14' => 'Fourteen',
			'15' => 'Fifteen',
			'16' => 'Sixteen',
			'17' => 'Seventeen',
			'18' => 'Eighteen',
			'19' => 'Nineteen',
			'20' => 'Twenty',
			'30' => 'Thirty',
			'40' => 'Forty',
			'50' => 'Fifty',
			'60' => 'Sixty',
			'70' => 'Seventy',
			'80' => 'Eighty',
			'90' => 'Ninety',
		);
		$digits = array(
			'',
			'Hundred',
			'Thousand',
			'Lakh',
			'Crore',
		);
		$output = array();
		while ($i < $digits_1) {
			$divider = ($i == 2) ? 10 : 100;
			$number = floor($no % $divider);
			$no = floor($no / $divider);
			$i += ($divider == 10) ? 1 : 2;
			if ($number) {
				$plural = (($counter = count((array)$str)) && $number > 9) ? 's' : null;
				$hundred = ($counter == 1 && $str[0]) ? '  ' : null;
				$str[] = ($number < 21) ? $words[$number] . " " . $digits[$counter] . $plural . " " . $hundred : $words[floor($number / 10) * 10] . " " . $words[$number % 10] . " " . $digits[$counter] . $plural . " " . $hundred;
			} else {
				$str[] = null;
			}
		}
		$str = array_reverse($str);
		$result = implode('', $str);
		$output[] .= ($point < 21) ? $words[$point] : $words[floor($point / 10) * 10] . " " . $words[$point % 10] . " ";
		$result1 = implode('', $output);
		if ($point > 0) {
			$result .= "Rupees And " . $result1 . " Paise Only";
		} else {
			$result .= "Rupees  " . " Only";
		}
		return $result;
	}

    public function mf_total_nums_row($table, $where) // RETURN TOTAL COUNT OF RECORDS
	{
		$qry = "SELECT count(*) as total from $table $where ";
		// exit();
		$result = $this->mf_query($qry);
		if ($this->mf_affected_rows() > 0) {
			$row = $this->mf_fetch_array($result);
			return $num_results = $row['total'];
		} else {
			return "";
		}
	}


    public function date2savenew($date) // CONVERT DATE TO STORE FORMAT (MYSQL FORMAT)
	{
		if ($date == "") {
			return "";
		} else {
			$dtArr = explode("/", $date);
			$newDt = $dtArr[2] . "-" . $dtArr[1] . "-" . $dtArr[0];
			return $newDt;
		}
	}


    public function datedash2save($date) // CONVERT DATE TO STORE FORMAT (MYSQL FORMAT)
	{
		if ($date == "") {
			return "";
		} else {
			$dtArr = explode("-", $date);
			$newDt = $dtArr[2] . "-" . $dtArr[1] . "-" . $dtArr[0];
			return $newDt;
		}
	}

    public function date2saveinline($date) // CONVERT DATE TO STORE FORMAT (MYSQL FORMAT)
	{
		if ($date == "0000-00-00") {
			return "";
		} else {
			$dtArr = explode("-", $date);
			$newDt = $dtArr[0] . "-" . $dtArr[2] . "-" . $dtArr[1];
			return $newDt;
		}
	}

    public function date2save($date) // CONVERT DATE TO STORE FORMAT (MYSQL FORMAT)
	{
		if ($date == "") {
			return "";
		} else {
			$dtArr = explode("/", $date);
			$newDt = $dtArr[2] . "-" . $dtArr[0] . "-" . $dtArr[1];
			return $newDt;
		}
	}

    public function datetime2save($date, $format = 'Y-m-d H:i:s') // CONVERT DATE & TIME TO STORE FORMAT (MYSQL FORMAT)
	{
		if ($date == "" || $date == '0000-00-00 00:00:00') {
			return "";
		} else {
			$dt = str_replace("/", "-", $date);
			$newDt = date($format, strtotime($dt));
			return $newDt;
		}
	}

	public function date2saveISO($date, $format = "Y-m-d H:i:s") // CONVERT DATE TO STORE FORMAT (MYSQL FORMAT)
	{
		if ($date == "" || $date == '0000-00-00 00:00:00') {
			return "";
		} else {
			$newDt = date($format, strtotime($date));
			return $newDt;
		}
	}

    public function timetosave($time) // CONVERT TIME TO STORE FORMAT (MYSQL FORMAT)
	{
		if ($time != "") {
			return date("H:i:s", strtotime($time));
		}
		return '';
	}

    public function timetodisp($time) // CONVERT TIME TO STORE FORMAT (MYSQL FORMAT)
	{
		if ($time != "" && $time != '0000-00-00 00:00:00') {
			return date("h:i A", strtotime($time));
		}
		return '';
	}

	function getDatesFromRange($start, $end, $format = 'Y-m-d') {
		$array = array();
		$interval = new DateInterval('P1D');
	
		$realEnd = new DateTime($end);
		$realEnd->add($interval);
	
		$period = new DatePeriod(new DateTime($start), $interval, $realEnd);
	
		foreach($period as $date) { 
			$array[] = $date->format($format); 
		}
	
		return $array;
	}
	
    public function GetTimeDiff($stime, $etime)
	{
		$seconds = strtotime($etime) - strtotime($stime);
		$hours = floor($seconds / 3600);
		$minutes = floor(($seconds / 60) % 60);
		$seconds = $seconds % 60;
		if ($hours > 0) {
			$hrString = "$hours hours, ";
		}
		if ($minutes > 0) {
			$minString = "$minutes minutes, ";
		}
		if ($seconds > 0) {
			$secString = "$seconds seconds, ";
		}
		$diffTimeStr = $hours > 0 ? "$hrString$minString$secString" : ($minutes > 0 ? "$minString$secString" : $secString);
		if (strtotime($etime) - strtotime($stime) > 0) {
			return substr($diffTimeStr, 0, -2);
		}
	}
	public function GetTimeDiffShort($stime, $etime)
	{
		$seconds = strtotime($etime) - strtotime($stime);
		$hours = floor($seconds / 3600);
		$minutes = floor(($seconds / 60) % 60);
		$seconds = $seconds % 60;
		if ($hours > 0) {
			$hrString = $hours . "h, ";
		}
		if ($minutes > 0) {
			$minString = $minutes . "m, ";
		}
		if ($seconds > 0) {
			$secString = $seconds . "s, ";
		}
		$diffTimeStr = $hours > 0 ? "$hrString$minString$secString" : ($minutes > 0 ? "$minString$secString" : $secString);
		if (strtotime($etime) - strtotime($stime) > 0) {
			return substr($diffTimeStr, 0, -2);
		}
	}
	public function dateDiffFormat($sDate, $eDate, $Format = '%a Days')
	{
		$datetime1 = date_create($sDate);
		$datetime2 = date_create($eDate);
		$interval = date_diff($datetime1, $datetime2);
		if ($sDate != '0000-00-00' && $eDate != '0000-00-00') {
			return $interval->format($Format);
		}
	}
	public function dateDifferenceFormat($sDate, $eDate, $Format = '%a Days')
	{
		$datetime1 = date_create($sDate);
		$datetime2 = date_create($eDate);
		$interval = date_diff($datetime1, $datetime2);
		if ($sDate != '0000-00-00' && $eDate != '0000-00-00') {
			return $interval->format($Format);
		} else {
			return 0;
		}
	}
	public function timeDifference($startTime = '', $endTime = '') // COUNT TIME BETWEEN TO TIMES
	{
		// return $startDate.''.$endDate;
		$startTimeConverted = strtotime($startTime);
		$endTimeConverted = strtotime($endTime);
		$Diff = $endTimeConverted - $startTimeConverted;
		$substractedTime = max(intval($Diff), 0);
		$timeDifference = round((float)abs($substractedTime) / 3600) + 1;
		// echo $dateDifference;
		return $timeDifference;
	}
	public function timeDifferenceMinutes($startTime = '', $endTime = '') // COUNT TIME BETWEEN TO TIMES
	{
		// return $startDate.''.$endDate;
		$date = date('Y-m-d');
		if (strtotime($endTime) < strtotime($startTime)) {
			$Ndate = date('Y-m-d', strtotime("+1 Day", strtotime($date)));
			$startTime = $date . " " . $startTime;
			$endTime = $Ndate . " " . $endTime;
		} else {
			$startTime = $date . " " . $startTime;
			$endTime = $date . " " . $endTime;
		}
		$startTimeConverted = strtotime($startTime);
		$endTimeConverted = strtotime($endTime);
		$Diff = $endTimeConverted - $startTimeConverted;
		$timeDifference = $Diff / 60;
		// echo $dateDifference;
		return $timeDifference;
	}
	public function timeDifferenceSeconds($startTime = '', $endTime = '') // COUNT TIME BETWEEN TO TIMES
	{
		$startTimeConverted = strtotime($startTime);
		$endTimeConverted = strtotime($endTime);
		$Diff = $endTimeConverted - $startTimeConverted;
		$timeDifference = $Diff;
		return $timeDifference;
	}
	public function timediffInMinutes($startDate = '', $endDate = '') // COUNT DAY BETWEEN TO DATES
	{
		//Convert them to timestamps.
		$startTimeConverted = strtotime($startDate);
		$endTimeConverted = strtotime($endDate);
		$substractedTime = abs($endTimeConverted - $startTimeConverted);
		return round((float)$substractedTime / 60, 2);
	}
	public function dateDifference($startDate = '', $endDate = '') // COUNT DAY BETWEEN TO DATES
	{
		$startDateConverted = strtotime($startDate);
		$endDateConverted = strtotime($endDate);
		$substractedDate = $endDateConverted - $startDateConverted;
		$substractedDate = max(intval($substractedDate), 0);
		$dateDifference = round((float)abs($substractedDate) / 86400) + 1;
		// echo $dateDifference;
		return $dateDifference;
	}
	public function date2dispnew($date) // CONVERT DATE TO DISPLAY FORMAT (INDIAN FORMAT)
	{
		if ($date == "" || $date == "0000-00-00") {
			return "";
		} else {
			return date("d/m/Y", strtotime($date));
		}
	}
	public function datetime2dispnew($date) // CONVERT DATE & TIME TO DISPLAY FORMAT (INDIAN FORMAT)
	{
		if ($date == "" || $date == "0000-00-00 00:00:00") {
			return "";
		} else {
			return date("d/m/Y H:i:s", strtotime($date));
		}
	}
	public function date2disp($date) // CONVERT DATE TO DISPLAY FORMAT (INDIAN FORMAT)
	{
		if ($date == "" || $date == "0000-00-00") {
			return "";
		} else {
			return date("m/d/Y", strtotime($date));
		}
	}
	public function convertDateTime($date, $format = 'd/m/Y H:i:s') // CONVERT DATE & TIME TO  FORMAT (INDIAN FORMAT)
	{
		if ($date == "" || $date == "0000-00-00 00:00:00" || $date == "0000-00-00" || $date == "00:00:00") {
			return "";
		} else {
			return date($format, strtotime($date));
		}
	}

    public function mf_unlink($imgpath) // REMOVE SPECIFIED IMAGE FROM FOLDER
	{
		if (is_file($imgpath)) {
			unlink($imgpath);
		}
	}
    public function mf_filerename($path, $oldname, $newname) // FUNCTION TO GET THE EXTENTION OF THE FILE
	{
		$pathinfo = pathinfo($path . $oldname);
		$ext = $pathinfo['extension'];
		rename($path . $oldname, $path . $newname . '.' . $ext);
		return $newname . '.' . $ext;
	}

    public function human_value($srt)
	{
		if ($srt != "") {
			return mb_convert_encoding($srt, "UTF-8", "Windows-1252");
		} else {
			return "N/A";
		}
	}

    public function human_valueDash($srt)
	{
		if ($srt != "") {
			return mb_convert_encoding($srt, "UTF-8", "Windows-1252");
		} else {
			return "-";
		}
	}

    public function null_value($srt)
	{
		if ($srt != "null") {
			return mb_convert_encoding($srt, "UTF-8", "Windows-1252");
		} else {
			return "";
		}
	}

    public function get_total($singleField, $table, $where)
	{
		$sel = $this->mf_query("select $singleField from $table where $where");
		if ($sel) {
			return mysqli_num_rows($sel);
		} else {
			return 0;
		}
	}
	public function mf_num_rows($query)
	{
		if ($query) {
			return mysqli_num_rows($query);
		} else {
			return 0;
		}
	}

    public function saveAddress($str) //Store address with special character
	{
		return htmlspecialchars($str, REPLACE_FLAGS, 'UTF-8');
	}

	public function viewAddress($str) //View address with special character
	{
		return utf8_encode($str);
	}

    function convertCamelCase($str)
	{
		$i = array("-", "_");
		$str = preg_replace('/([a-z])([A-Z])/', "\\1 \\2", $str);
		$str = preg_replace('@[^a-zA-Z0-9\-_ ]+@', '', $str);
		$str = str_replace($i, ' ', $str);
		$str = str_replace(' ', '', ucwords(strtolower($str)));
		$str = strtolower(substr($str, 0, 1)) . substr($str, 1);
		return $str;
	}

	function file_decode($vFileData, $fixPath, $fileName = "")
	{
		$image_parts = explode(";base64,", $vFileData);
		$image_type_aux = explode("/", $image_parts[0]);
		$image_base64 = base64_decode($image_parts[1]);

		$orgFileName = $fileName;
		$orgFileName = preg_replace('/[^a-zA-Z0-9_ ()-.]/s', '', $orgFileName);
		$orgFileName = preg_replace('/[ ]/s', '_-_', $orgFileName);
		$expName = explode(".", $orgFileName);

		$vNewFileName = uniqid() . '.' . $image_type_aux[1];

		$year = date("y");
		$month = date("m");
		$directory = "$year/$month/";
		$fullPath = $fixPath . $vNewFileName;
		if (!is_dir($fixPath)) {
			mkdir($fixPath, 0777, true);
		}
		if (file_put_contents($fullPath, $image_base64)) {
			return $vNewFileName;
		}
	}
}

$mfp= new crud(); 
