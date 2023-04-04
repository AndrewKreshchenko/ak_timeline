
document.addEventListener('DOMContentLoaded', function(e) {
  // DOM element where the Timeline will be attached
  const visBlock = document.querySelector('[data-js="timeline-horizontal"]');
  const dataBlock = visBlock.parentNode.querySelector('[data-js="timeline-data"]');

  const handleClickVisItem = (e) => {
    e.preventDefault();

    // 1) get target element and get points helper info

    // 2) Retrieve information from template and show it for user
    // dataBlock
  }

  const container = document.querySelector('.tx-timeline[data-tl_id]');
  const dataId = container.dataset.tl_id;
  const dataMore = container.dataset.url;

  // GET data by action

  const xhr = new XMLHttpRequest();

  xhr.onreadystatechange = function() {
    if (xhr.readyState === 4) {
      try {
        var data = JSON.parse(xhr.responseText)

        console.log('Status ' + xhr.readyState);

        if (data.points && data.points.length) {
          // Prepare visual part
          const dataVisual = [];

          data.points.forEach((point, i) => {
            const dateValue = point.date.date.split(' ')[0].split('-');
            const date = {
              year: Number(dateValue[0]),
              month: Number(dateValue[1]),
              day: Number(dateValue[2])
            };

            const dateString = [date.year, date.month, date.day].join('-');

            dataVisual.push({
              id: 'segment' + point.id,
              type: 'point',
              start: dateString,
              content: point.title,
            });
          });

          // Create a DataSet (allows two way data-binding)
          const items = new vis.DataSet(dataVisual);

          // Configuration for the Timeline
          const options = {};

          // Create a Timeline
          const timeline = new vis.Timeline(visBlock, items, options);

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
