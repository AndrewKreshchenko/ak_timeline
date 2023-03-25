
document.addEventListener('DOMContentLoaded', function(e) {
  // DOM element where the Timeline will be attached
  const visBlock = document.querySelector('[data-js="timeline-horizontal"]');
  const dataBlock = visBlock.parentNode.querySelector('[data-js="timeline-data"]');

  // Create a DataSet (allows two way data-binding)
  const items = new vis.DataSet([
    { id: 'segment1', content: "item 1", start: "2022-04-20" },
    { id: 'segment2', content: "item 4", start: "2023-01-16", end: "2023-03-08" },
    { id: 'segment6', content: "item 6", start: "2022-12-27", type: "point" }
  ]);

  // Configuration for the Timeline
  const options = {};

  // Create a Timeline
  const timeline = new vis.Timeline(visBlock, items, options);

  // AJAX
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

  console.log(dataId);
  const xhr = new XMLHttpRequest();

  xhr.onreadystatechange = () => {
    if (xhr.readyState === 4) {
      console.log(xhr.readyState, JSON.parse(xhr.responseText));

      if (xhr.responseText.points) {
        container.querySelectorAll('.vis-item-content').forEach((point) => {
          point.addEventListener('click', handleClickVisItem);
        });
      }
    }
  };

  // Test using middleware:
  // xhr.open('GET', 'http://localhost:8000/index.php?tlinfo=true&pid=' + dataId);

  xhr.open('GET', dataMore);
  xhr.send();

});
