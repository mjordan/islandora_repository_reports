/**
@file
Javascript that renders a Chart.js chart.
*/

(function (Drupal, $) {
  "use strict";

  var IslandoraRepositoryReportsChartCanvas = document.getElementById('islandora-repository-reports-chart');
  var IslandoraRepositoryReportsChartType = drupalSettings.islandora_repository_reports.chart_type;

  if (IslandoraRepositoryReportsChartType == 'pie') {
    var IslandoraRepositoryReportsPieChartData = drupalSettings.islandora_repository_reports.chart_data;
    var IslandoraRepositoryReportsPieChartTitle = drupalSettings.islandora_repository_reports.chart_title;
    if (IslandoraRepositoryReportsPieChartData != null) {
      var IslandoraRepositoryReportsPieChart = new Chart(IslandoraRepositoryReportsChartCanvas, {
        type: 'pie',
        data: IslandoraRepositoryReportsPieChartData,
        options: {
          layout: {
            padding: {
              top: 50,
              bottom: 100,
            }
          },
          title: {
            display: true,
            fontSize: 16,
	    text: [IslandoraRepositoryReportsPieChartTitle, 'Click on entries in the legend to turn them on/off.']
          }
        }
      });
    }
  }

  if (IslandoraRepositoryReportsChartType == 'bar') {
    var IslandoraRepositoryReportsBarChartData = drupalSettings.islandora_repository_reports.chart_data;
    var IslandoraRepositoryReportsBarChartTitle = drupalSettings.islandora_repository_reports.chart_title;
    if (IslandoraRepositoryReportsBarChartData != null) {
      var IslandoraRepositoryReportsBarChart = new Chart(IslandoraRepositoryReportsChartCanvas, {
        type: 'bar',
        data: IslandoraRepositoryReportsBarChartData,
        options: {
          layout: {
            padding: {
              top: 50,
              bottom: 100,
            }
          },
          title: {
            display: true,
            fontSize: 16,
	    text: IslandoraRepositoryReportsBarChartTitle
          },
          scales: {
            yAxes: [{
                ticks: {
                    beginAtZero: true,
                    stepSize: 1,
                }
            }]
          }
        }
      });
    }
  }

})(Drupal, jQuery);
