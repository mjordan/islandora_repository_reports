/**
@file
Javascript that renders a Chart.js line chart.
*/

(function (Drupal, $) {
  "use strict";

  var IslandoraRepositoryReportsPieChartCanvas = document.getElementById('islandora-repository-reports-chart');
  var IslandoraRepositoryReportsPieChartData = drupalSettings.islandora_repository_reports.chart_data;

  if (IslandoraRepositoryReportsPieChartData != null) {
    var IslandoraRepositoryReportsPieChart = new Chart(IslandoraRepositoryReportsPieChartCanvas, {
      type: 'pie',
      data: IslandoraRepositoryReportsPieChartData,
      options: {
        layout: {
          padding: {
            top: 50,
            bottom: 100,
          }
        }
      }
    });
  }

})(Drupal, jQuery);
