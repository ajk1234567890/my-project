<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.4.0/Chart.min.js"></script>

<?php

echo "
	<form action='".$_SERVER['PHP_SELF']."' method='post'>
	START DATE:
	<input type='date' name='start_date'>
	
	END DATE:
	<input type='date' name='end_date'>
	
	<input type='submit' value='submit' name='submit'> 
	</form>
";
$total_no_of_closest_asteroid=array();
$most_fastest_speed_of_asteroid=array();
$average_speed_of_asteroid=array();
$dates=array();
if($_POST['submit'] == 'submit')
{


	$s_date=$_POST['start_date'];
	$e_date=$_POST['end_date'];
	
	$ch= curl_init("https://api.nasa.gov/neo/rest/v1/feed?start_date=".date("Y-m-d",strtotime($s_date))."&end_date=".date("Y-m-d",strtotime($e_date))."&api_key=DEMO_KEY");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
	$var=curl_exec($ch);
	$var1=json_decode($var, true);
	$var2=$var1['near_earth_objects'];
	//print_r($var2);
	
	while (strtotime($s_date) <= strtotime($e_date)) {
		
		foreach($var2 as $key1=>$value1){
			
			while($s_date==$key1)
			{
			$dates[]=$key1;
				echo $s_date.'<br /><br />';
				
				foreach($value1 as $key2=>$value2)
				{					
					foreach($value2['close_approach_data'] as $key3=>$value3){
						$fastest[]=$value3['relative_velocity']['kilometers_per_hour'];	
						$closest[]=$value3['miss_distance']['kilometers'];
						$count_closest=count($closest);
						$count_fastest=count($fastest);	
					}						
				}
				//------------------------ closest NEO (in kilometer)-----------------------------
				echo "<b>CLOSEST</b>";
				echo "<br />";
				echo "<b>Total Closest Asteroids in ascending order:</b><br />";
				$closest_value=sorting($closest);
				print_r($closest_value);
				echo "<br />";
				echo "<b>Total number of asteroids</b><br />".$count_closest;
				$sort_closest=array_slice(explode(",",$closest_value), 0, 3);
				echo "<br />";
				echo "<b>top 3 closest asteroid (in kilometer) :</b><br />";
				//print_r($sort_closest);
				print_r(str_replace(array("\"","[","]"),"",json_encode($sort_closest)));
				
				//------------------------- speed of closest NEO (in kilometer per hour)--------------------
				echo "<br /><br /><b>FASTEST</b>";
				echo "<br />";
				echo "<b>Total Fastest Asteroids in decending order:</b><br />";
				$fastest_value=FastestSorting($fastest);
				print_r($fastest_value);
				echo "<br />";
				echo "<b>Total number of fastest asteroids:</b><br />".$count_fastest;
				$most_fastest=array_slice(explode(",",$fastest_value), 0, 1);  // to the graph purpose
				$sort_fastest=array_slice(explode(",",$fastest_value), 0, 3);
				echo "<br />";
				echo "<b>top 3 fastest asteroid (in kilometer per hour):</b><br />";
				//print_r($sort_closest);
				print_r(str_replace(array("\"","[","]"),"",json_encode($sort_fastest)));
				
				//------------------------- average speed of NEO (in kilometer per hour)------------------
				echo "<br /><br /><b>AVERAGE SPEED</b>";
				echo "<br />";
				$sum_fastest=array_sum($fastest);
				$AverageSpeesd=$sum_fastest/$count_fastest;
				print_r($AverageSpeesd);
				echo " ( sum of fastest speed / count of fastest speed )";
				
				//--------------------------array variable to show data in bar chart--------------------
				$total_no_of_closest_asteroid[]=$count_closest;
				$most_fastest_speed_of_asteroid[]=$most_fastest;
				$average_speed_of_asteroid[]=$AverageSpeesd;
				
				echo "<br /><br />";
				$s_date= date ("Y-m-d", strtotime("+1 day", strtotime($s_date)));	
			}
			
		}
			
	}

	curl_close();
}

$datesvalue=json_encode($dates);
//print_r($datesvalue);
$closest_asteroids=json_encode($total_no_of_closest_asteroid);
$fastest_asteroids=$most_fastest_speed_of_asteroid;
$arraySingle = call_user_func_array('array_merge', $fastest_asteroids); // convert multi dimentional array into single dim.
$fastest_asteroid_encoded=json_encode($arraySingle);
$averageSpeedOf_asteroids=json_encode($average_speed_of_asteroid);
//print_r($fastest_asteroid_encoded);

//----------------------------------- function -----------------
function sorting($vr2){
//$vr2=explode(",",$vR);
$arr=array();
for($i=0;$i<=count($vr2);$i++){
            for ($j = $i + 1; $j < count($vr2); ++$j)
            {
                if ($vr2[$i] > $vr2[$j]) 
                {
                    $a =  $vr2[$i];
                    $vr2[$i] = $vr2[$j];
                    $vr2[$j] = $a;
                }
            }
}
return str_replace(array("\"","[","]"),"",json_encode($vr2));
}

function FastestSorting($vr2){
//$vr2=explode(",",$vR);
$arr=array();
for($i=0;$i<=count($vr2);$i++){
            for ($j = $i + 1; $j < count($vr2); ++$j)
            {
                if ($vr2[$i] < $vr2[$j]) 
                {
                    $a =  $vr2[$i];
                    $vr2[$i] = $vr2[$j];
                    $vr2[$j] = $a;
                }
            }
}
return str_replace(array("\"","[","]"),"",json_encode($vr2));
}

?>	



<h4>Closest (Number of asteroids to every day)</h4>
<canvas id="myChart" width="400" height="400"></canvas>
<h4>Fastest (Most fastest speed of asteroids of every day )</h4>
<canvas id="myChart1" width="400" height="400"></canvas>
<h4>Average Speed (average speed of asteroid to per day)</h4>
<canvas id="myChart2" width="400" height="400"></canvas>


<script>
	var ctx = document.getElementById('myChart').getContext('2d');
	var myChart = new Chart(ctx, {
	    type: 'bar',
	    data: {
	        labels: <?php echo $datesvalue ?>,
	        datasets: [{
	            label: 'Closest Astroid',
	            data: <?php echo $closest_asteroids?>,
	            borderWidth: 1
	        }]
	    },
	    options: {
	        scales: {
	            yAxes: [{
	                ticks: {
	                    beginAtZero: true
	                }
	            }]
	        }
	    }
	});
	var ctx = document.getElementById('myChart1').getContext('2d');
	var myChart = new Chart(ctx, {
	    type: 'bar',
	    data: {
	        labels: <?php echo $datesvalue ?>,
	        datasets: [{
	            label: 'Fastest Astroid',
	            data: <?php echo $fastest_asteroid_encoded ?>,
	            borderWidth: 1
	        }]
	    },
	    options: {
	        scales: {
	            yAxes: [{
	                ticks: {
	                    beginAtZero: true
	                }
	            }]
	        }
	    }
	});
	var ctx = document.getElementById('myChart2').getContext('2d');
	var myChart = new Chart(ctx, {
	    type: 'bar',
	    data: {
	        labels: <?php echo $datesvalue ?>,
	        datasets: [{
	            label: 'Average Speed of Astroid',
	            data: <?php echo $averageSpeedOf_asteroids?>,
	            borderWidth: 1
	        }]
	    },
	    options: {
	        scales: {
	            yAxes: [{
	                ticks: {
	                    beginAtZero: true
	                }
	            }]
	        }
	    }
	});
	
</script>
