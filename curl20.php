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

if($_POST['submit'] == 'submit')
{
	$s_date=$_POST['start_date'];
	$e_date=$_POST['end_date'];
	
	$ch= curl_init("https://api.nasa.gov/neo/rest/v1/feed?start_date=".date("Y-m-d",strtotime($s_date))."&end_date=".date("Y-m-d",strtotime($e_date))."&api_key=DEMO_KEY");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
	$var=curl_exec($ch);
	$var1=json_decode($var, true);
	$var2=$var1['near_earth_objects'];
	
	
	while (strtotime($s_date) <= strtotime($e_date)) {
		
		foreach($var2 as $key1=>$value1){
			
			while($s_date==$key1)
			{
				echo $s_date.'<br /><br />';
				//$dates[]=$key1;
				foreach($value1 as $key2=>$value2)
				{					
					foreach($value2['close_approach_data'] as $key3=>$value3){
						$fastest[]=$value3['relative_velocity']['kilometers_per_hour'];	
						//print_r($fastest);
						$closest[]=$value3['miss_distance']['kilometers'];
						//print_r($fastest);
						$avgspeed[]=$value3['relative_velocity']['kilometers_per_second'];
						//print_r($fastest);
					}						
				}
				echo "fastest<br />";
				$fastest_value=implode(",",$fastest);
				print_r($fastest_value);
				echo "<br />";
				echo "closest<br />";
				$closest_value=implode(",",$closest);
				print_r($closest_value);
				echo "<br />";
				echo "avgspeed<br />";
				$avgspeed_value=implode(",",$avgspeed);
				print_r($avgspeed_value);
				echo "<br /><br />";
				$s_date= date ("Y-m-d", strtotime("+1 day", strtotime($s_date)));	
			}
		}
			
	}
	/*
	print_r("closest value".$closest.'<br />');
	print_r("fastest value".$fastest.'<br />');
	print_r("avgspeed value".$avgspeed.'<br />');
	
	$count=count($dates);
	$datesvalue=json_encode($dates);
	$closest_value=json_encode(array_slice($closest,0,$count_closest,true));
	$fastest_value=json_encode(array_slice($fastest,0,$count_fastest,true));
	$avgspeed_value=json_encode(array_slice($avgspeed,0,$count_avgspeed,true));
	*/
	//print_r("closest value".$closest_value.'<br />');
	//print_r("fastest value".$fastest_value.'<br />');
	//print_r("avgspeed value".$avgspeed_value.'<br />');
	curl_close();	
}	
?>

<h4>Closest</h4>
<canvas id="myChart" width="400" height="400"></canvas>
<h4>Fastest</h4>
<canvas id="myChart1" width="400" height="400"></canvas>
<h4>Avg Speed</h4>
<canvas id="myChart2" width="400" height="400"></canvas>

<script>
	var ctx = document.getElementById('myChart').getContext('2d');
	var myChart = new Chart(ctx, {
	    type: 'bar',
	    data: {
	        labels: <?php echo $datesvalue;?>,
	        datasets: [{
	            label: 'Closest Astroid',
	            data: <?php echo $closest_value;?>,
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
	var ctx1 = document.getElementById('myChart1').getContext('2d');
	var myChart1 = new Chart(ctx1, {
	    type: 'bar',
	    data: {
	        labels: <?php echo $datesvalue;?>,
	        datasets: [{
	            label: 'Fastest Astroid',
	            data: <?php echo $fastest_value;?>,
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
	var ctx2 = document.getElementById('myChart2').getContext('2d');
	var myChart2 = new Chart(ctx2, {
	    type: 'bar',
	    data: {
	        labels: <?php echo $datesvalue;?>,
	        datasets: [{
	            label: 'Avg Speeds Astroid',
	            data: <?php echo $avgspeed_value;?>,
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

 