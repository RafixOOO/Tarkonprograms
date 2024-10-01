<!DOCTYPE html>
<?php require_once 'auth.php';
require_once("dashbordssql.php"); ?>

<html lang="pl">
<head>
<?php require_once("globalhead.php"); ?>

</head>

<body class="p-3 mb-2 bg-light bg-gradient text-dark" id="error-container">


<!-- 2024 Created by: Rafał Pezda-->
<!-- link: https://github.com/RafixOOO -->

<?php if(isSidebar()==0){ ?>
<div class="container-fluid" style="width:80%;margin-left:14%;">
    <?php }else if(isSidebar()==1){ ?>
        <div class="container-fluid" style="width:90%; margin: 0 auto;">
        <?php } ?>
                <div class="card card-round" style="width:60%;float:left; margin-right:5%;margin-left:1%;max-height:460px">
                  <div class="card-header">
                    <div class="card-head-row">
                      <div class="card-title">Liczba wykonanych detali</div>
                    </div>
                  </div>
                  <div class="card-body" style="min-height: 475px;">
                    <div class="chart-container">
                      <canvas id="statisticsChart"></canvas>
                    </div>
                    <div id="myChartLegend"></div>
                  </div>
                </div>
				<div class="col-md-4" style="float:left;">
                <div class="card card-primary card-round">
                  <div class="card-header">
                    <div class="card-head-row">
                      <div class="card-title">Maksymalna liczba wykonanych detali</div>
                    </div>
                    <div class="card-category" id="dateRangeDisplay"><script>
    // Funkcja do formatowania daty do DD.MM.RRRR
    function formatDate(date) {
        let day = date.getDate();
        let month = date.getMonth() + 1;
        let year = date.getFullYear();

        // Dodajemy zero przed jednocyfrowymi dniami i miesiącami
        if (day < 10) {
            day = '0' + day;
        }
        if (month < 10) {
            month = '0' + month;
        }

        return day + '.' + month + '.' + year;
    }

    // Obliczamy daty: dzisiejsza data i data sprzed 30 dni
    let today = new Date();
    let thirtyDaysAgo = new Date();
    thirtyDaysAgo.setDate(thirtyDaysAgo.getDate() - 30);

    // Formatujemy obie daty do pożądanego formatu
    let formattedToday = formatDate(today);
    let formattedThirtyDaysAgo = formatDate(thirtyDaysAgo);

    // Tworzymy tekst przedziału datowego i umieszczamy go w elemencie div
    let dateRangeText = formattedThirtyDaysAgo + ' - ' + formattedToday;
    document.getElementById('dateRangeDisplay').innerText = dateRangeText;
</script></div>
                  </div>
                  <div class="card-body pb-0">
                    <div class="mb-4 mt-2">
					<h1>
						<?php
						while ($row = sqlsrv_fetch_array($sumadetalidata, SQLSRV_FETCH_ASSOC)) {  

							echo $row['Suma_AmountDone'];
					}
						?>
                      </h1>
                    </div>
                    <div class="pull-in">
                      <canvas id="dailySalesChart"></canvas>
                    </div>
                  </div>
                </div>
				<?php while ($row = $pracownicy->fetch(PDO::FETCH_ASSOC)) {
        // Tutaj możesz przetwarzać każdy wiersz wynikowy
        echo ' <div class="card card-round">
                  <div class="card-body pb-0">
                    <h2 class="mb-2">'.$row['diff_count'].'</h2>
                    <p class="text-muted">Aktualna liczba pracowników na produkcji</p>
                    <div class="pull-in sparkline-fix">
                      <div id="lineChart"></div>
                    </div>
                  </div>
                </div>';
    }
 ?>
               
              </div>
              
			  <div style="clear:both;"></div>
			  <div class="col-md-8" style="width:100%; margin: 0 auto;">
                <div class="card card-round">
                  <div class="card-header">
                    <div class="card-head-row card-tools-still-right">
                      <div class="card-title">Liczba zrobionych detali na miesiąc przez daną osobę</div>
                    </div>
                  </div>
                  <div class="card-body p-0">
                    <div class="table-responsive">
                      <!-- Projects table -->
                      <table class="table align-items-center mb-0">
                        <thead class="thead-light">
							
                          <tr>
                            <th scope="col">Osoba</th>
                            <th scope="col" class="text-end">Messer</th>
                            <th scope="col" class="text-end">Ręcznie i Piła</th>
                            <th scope="col" class="text-end">Razem</th>
                          </tr>
                        </thead>
                        <tbody>
						<?php while ($row = sqlsrv_fetch_array($osoby, SQLSRV_FETCH_ASSOC)) { ?>
                          <tr>
                            <th scope="row">
								<?php echo $row['Osoba']; ?>
						</th>
                            <td class="text-end"><?php echo $row['messer']; ?></td>
                            <td class="text-end"><?php echo $row['recznie']; ?></td>
                            <td class="text-end">
							<?php echo $row['suma']; ?>
                            </td>
                          </tr>
						  <?php } ?>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
			  </div>
              <?php require_once("globalnav.php"); ?>
			  
</body>

<?php 
$reczniedane='';
$messerdane='';
$v630dane='';
$v630first=1;
$messerfirst=1;
$reczniefirst=1;
$dzienniefirst=1;
$miesiace='';
$sumadziendane='';
$dni='';
while ($row = sqlsrv_fetch_array($V630data, SQLSRV_FETCH_ASSOC)) { 
	if($v630first==1){
		$v630dane=$v630dane.$row['Suma_AmountDone'];
		$v630first=0;
	}else{
		$v630dane=$v630dane.','.$row['Suma_AmountDone'];
	}

}

while ($row = sqlsrv_fetch_array($messerdata, SQLSRV_FETCH_ASSOC)) {  
	if ($messerfirst == 1) {
        $messerdane .= $row['Suma_AmountDone'];
        $miesiace .= '"' . $row['Miesiac']->format('Y-m') . '"';
        $messerfirst = 0;
    } else {
        $messerdane .= ',' . $row['Suma_AmountDone'];
        $miesiace .= ', "' . $row['Miesiac']->format('Y-m') . '"';
    }

}

while ($row = sqlsrv_fetch_array($reczniedata, SQLSRV_FETCH_ASSOC)) {  
	if ($reczniefirst == 1) {
        $reczniedane .= $row['Suma_AmountDone'];
        $reczniefirst = 0;
    } else {
        $reczniedane .= ',' . $row['Suma_AmountDone'];
    }

}
while ($row = sqlsrv_fetch_array($sumadniadetalidata, SQLSRV_FETCH_ASSOC)) {  
	if ($dzienniefirst == 1) {
        $sumadziendane .= $row['Suma_AmountDone'];
		$dni .= '"' . $row['Data']->format('Y-m-d') . '"';
        $dzienniefirst = 0;
    } else {
        $sumadziendane .= ',' . $row['Suma_AmountDone'];
		$dni .= ', "' . $row['Data']->format('Y-m-d') . '"';
    }

}
?>
<script>
        
        var ctx = document.getElementById('statisticsChart').getContext('2d');

var statisticsChart = new Chart(ctx, {
	type: 'line',
	data: {
		labels: [<?php echo $miesiace;?>],
		datasets: [ {
			label: "Ręcznie i Piła",
			borderColor: '#f3545d',
			pointBackgroundColor: 'rgba(243, 84, 93, 0.6)',
			pointRadius: 0,
			backgroundColor: 'rgba(243, 84, 93, 0.4)',
			legendColor: '#f3545d',
			fill: true,
			borderWidth: 2,
			data: [<?php echo $reczniedane;?>]
		}, {
			label: "Messer",
			borderColor: '#177dff',
			pointBackgroundColor: 'rgba(23, 125, 255, 0.6)',
			pointRadius: 0,
			backgroundColor: 'rgba(23, 125, 255, 0.4)',
			legendColor: '#177dff',
			fill: true,
			borderWidth: 2,
			data: [<?php echo $messerdane;?>]
		}, {
			label: "V630",
			borderColor: '#fdaf4b',
			pointBackgroundColor: 'rgba(253, 175, 75, 0.6)',
			pointRadius: 0,
			backgroundColor: 'rgba(253, 175, 75, 0.6)',
			legendColor: '#fdaf4b',
			fill: true,
			borderWidth: 2,
			data: [<?php echo $v630dane;?>]
		},]
	},
	options : {
		responsive: true, 
		maintainAspectRatio: false,
		legend: {
			display: false
		},
		tooltips: {
			bodySpacing: 4,
			mode:"nearest",
			intersect: 0,
			position:"nearest",
			xPadding:10,
			yPadding:10,
			caretPadding:10
		},
		layout:{
			padding:{left:5,right:5,top:15,bottom:15}
		},
		scales: {
			yAxes: [{
				ticks: {
					fontStyle: "500",
					beginAtZero: false,
					maxTicksLimit: 5,
					padding: 10
				},
				gridLines: {
					drawTicks: false,
					display: false
				}
			}],
			xAxes: [{
				gridLines: {
					zeroLineColor: "transparent"
				},
				ticks: {
					padding: 10,
					fontStyle: "500"
				}
			}]
		}, 
		legendCallback: function(chart) { 
			var text = []; 
			text.push('<ul class="' + chart.id + '-legend html-legend">'); 
			for (var i = 0; i < chart.data.datasets.length; i++) { 
				text.push('<li><span style="background-color:' + chart.data.datasets[i].legendColor + '"></span>'); 
				if (chart.data.datasets[i].label) { 
					text.push(chart.data.datasets[i].label); 
				} 
				text.push('</li>'); 
			} 
			text.push('</ul>'); 
			return text.join(''); 
		}  
	}
});

var myLegendContainer = document.getElementById("myChartLegend");

// generate HTML legend
myLegendContainer.innerHTML = statisticsChart.generateLegend();

var dailySalesChart = document.getElementById('dailySalesChart').getContext('2d');

var myDailySalesChart = new Chart(dailySalesChart, {
	type: 'line',
	data: {
		labels:[<?php echo $dni;?>],
		datasets:[ {
			label: "Liczba detali", fill: !0, backgroundColor: "rgba(255,255,255,0.2)", borderColor: "#fff", borderCapStyle: "butt", borderDash: [], borderDashOffset: 0, pointBorderColor: "#fff", pointBackgroundColor: "#fff", pointBorderWidth: 1, pointHoverRadius: 5, pointHoverBackgroundColor: "#fff", pointHoverBorderColor: "#fff", pointHoverBorderWidth: 1, pointRadius: 1, pointHitRadius: 5, data: [<?php echo $sumadziendane;?>]
		}]
	},
	options : {
		maintainAspectRatio:!1, legend: {
			display: !1
		}
		, animation: {
			easing: "easeInOutBack"
		}
		, scales: {
			yAxes:[ {
				display:!1, ticks: {
					fontColor: "rgba(0,0,0,0.5)", fontStyle: "bold", beginAtZero: !0, maxTicksLimit: 10, padding: 0
				}
				, gridLines: {
					drawTicks: !1, display: !1
				}
			}
			], xAxes:[ {
				display:!1, gridLines: {
					zeroLineColor: "transparent"
				}
				, ticks: {
					padding: -20, fontColor: "rgba(255,255,255,0.2)", fontStyle: "bold"
				}
			}
			]
		}
	}
});

    </script>
</html>
  