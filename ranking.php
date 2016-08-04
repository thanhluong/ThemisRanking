<head>
	<meta charset="UTF-8">
	<title>
		Bảng xếp hạng - DNTL
	</title>

	<link rel="stylesheet" href="TableStyle.css" />

</head>
<body>

<center>
<table border="0">
<?php
	$Student = array();
	$Problem = array();
	$StudentSum = array();

	$usr = "";
	$prob = "";
	$mark = "";

	$Content = ""; //Page content

	function _Print($content){
		echo "<h1>" . $content . "</h1><br>";
	}
	function CollectData($FileDir){
		$FileHdl = fopen($FileDir,"r");
		$Line = fgets($FileHdl);
		$Line = substr($Line, 3, -1);
		fclose($FileHdl);
		//---------------
		global $usr;
		global $prob; 
		global $mark;
		//---------------
		$usr = "";
		$prob = "";
		$mark = "";
		$n = strlen($Line);
		$part = 1;
		//---------------
		for($i = 0; $i < $n; $i++){
			$c = $Line[$i];
			$o = ord($c);
			if((127 < $o) && ($o != 226)) continue;//ignore redundant char
			// echo $c;
			if($part == 1){
				if(ord($c) == 226){//triangular bullet
					$part = 2;
					continue;
				}
				$usr .= $c;
				continue;
			}
			if($part == 2){
				if($c == ':'){
					$part = 3;
					continue;
				}
				$prob .= $c;
				continue;
			}
			if($c == ' ') continue;
			if($part==3){
				if((ord($c) < ord('0')) || (ord('9') < ord($c))){ //not judged
					$mark = "0";
					break;
				}
				$part++;
			}
			$mark .= $c;
		}
		$prob = strtoupper($prob);
		// echo $usr . "<br>";
		// echo $Line . "<br>";
	}
	function UpdateScore($name,$task,$score){
		global $Student;
		if(array_key_exists($name, $Student)){
			if(array_key_exists($task, $Student[$name])){
				$Student[$name][$task] = max($Student[$name][$task], $score);//maximize
				return;
			}
			$Student[$name][$task] = $score;
			return;
		}
		$new_solved_list = array($task=>$score);
		$Student[$name] = $new_solved_list;
		// print_r($Student[$name]); echo "<br>";
	}
	function UpdateProblemList($task){
		global $Problem;
		if(in_array($task, $Problem)) return;//already added
		array_push($Problem, $task);
	}
	function ScanLogs(){
		global $usr;
		global $prob;
		global $mark;
		$LogDir = "../submit/Logs";
		$FileList = scandir($LogDir);
		foreach($FileList as $FileName){
			if($FileName == ".") continue;
			if($FileName == "..") continue;
			CollectData($LogDir . "/" . $FileName);
			// echo $mark . "<br>";
			UpdateProblemList($prob);
			UpdateScore($usr,$prob,$mark);
			// echo "#" . $usr . " " . $prob . " ". $mark . "<br>";
		}
	}
	function SortAchievement(){
		global $Student;
		global $StudentSum;
		foreach($Student as $Name=>$SolvedList){
			$Sum = 0;
			foreach($SolvedList as $Task=>$Score){
				$Sum += $Score;
			}
			$StudentSum[$Name] = $Sum;
		}
		arsort($StudentSum);
	}
	function PrintFirstRow(){
		global $Content;
		global $Problem;
		$Content .= "<tr>";
		$Content .= "<td><center><b>Thí sinh</b></center></td>";
		foreach($Problem as $Name){
			$Content .= "<td><center><b>" . $Name . "</b></center></td>";
		}
		$Content .= "<td><center><b>Tổng điểm</b></center></td>";
		$Content .= "</tr>";
	}
	function PrintScoreBoard(){
		global $Content;
		global $Student;
		global $Problem;
		global $StudentSum;
		foreach($StudentSum as $Name=>$SumScore){
			$Content .= "<tr>";//Create new line
			$Content .= "<td><b>" . $Name . "</b></td>";
			for($i = 0; $i < count($Problem); $i++){
				$TaskName = $Problem[$i];
				$Content .= "<td><center>";
				if(array_key_exists($TaskName, $Student[$Name])){
					$Content .= $Student[$Name][$TaskName] . "</center></td>";
					continue;
				}
				$Content .= "0</td>";//user haven't solved - default score is 0
			}
			$Content .= "<td><center>" . $SumScore . "</center></td>";
			$Content .= "</tr>";
		}
	}
	// echo ord("�");
	ScanLogs();
	SortAchievement();
	PrintFirstRow();
	PrintScoreBoard();
	echo $Content;
?>
</table>
</center>
</body>