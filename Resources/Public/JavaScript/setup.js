// moment.js

document.addEventListener('DOMContentLoaded', function(e) {
  // DOM element where the Timeline will be attached
  var visBlock = document.querySelector('[data-js="timeline-horizontal"]');

  if (!visBlock) {
    console.warn('%cPoints are not exist for the Timeline yet, or error in Timeline found. Please check settings.', 'padding:15px;font-size:12px;font-weight:bold;');
    return;
  }

  // var dataBlock = visBlock.parentNode.querySelector('[data-js="timeline-data"]');

  var handleClickVisItem = function(e) {
    e.preventDefault();

    // 1) get target element and get points helper info

    // 2) Retrieve information from template and show it for user
    // dataBlock
  }

  var container = document.querySelector('.tx-timeline[data-tl_id]');
  // var dataPageId = container.dataset.tl_pid;
  // var dataId = container.dataset.tl_id;
  var dataMore = container.dataset.url;

  // GET data by action

  // console.log(dataId);
  var xhr = new XMLHttpRequest();

  xhr.onreadystatechange = function() {
    if (xhr.readyState === 4) {
      try {
        var data = JSON.parse(xhr.responseText)

        console.log('Status ' + xhr.readyState);

        if (data.points && data.points.length) {
          // Prepare visual part
          var dataVisual = [];

          data.points.forEach(function(point, i) {
            var dateValue = point.date.date.split(' ')[0].split('-');
            var date = {
              year: Number(dateValue[0]),
              month: Number(dateValue[1]),
              day: Number(dateValue[2])
            };

            var dateString = [date.year, date.month, date.day].join('-');

            dataVisual.push({
              id: 'segment' + point.id,
              type: 'point',
              start: dateString,
              content: point.title,
            });
          });

          // Create a DataSet (allows two way data-binding)
          var items = new vis.DataSet(dataVisual);

          // Configuration for the Timeline
          var options = {};

          // Create a Timeline
          var timeline = new vis.Timeline(visBlock, items, options);

          // Open content block by clicked point
          container.querySelectorAll('.vis-item-content').forEach((point) => {
            point.addEventListener('click', handleClickVisItem);
          });
        }
      } catch(e) {
        console.error(e);
      }
    }
  };

  // Test using middleware:
  // xhr.open('GET', 'http://localhost:8000/index.php?tlinfo=true&pid=' + dataId);

  xhr.open('GET', dataMore);
  xhr.send();

});
