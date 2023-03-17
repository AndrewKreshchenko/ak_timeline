
document.addEventListener('DOMContentLoaded', function(e) {
  // DOM element where the Timeline will be attached
  const container = document.querySelector('[data-js="timeline-horizontal"]');
  const dataContainer = container.parentNode.querySelector('[data-js="timeline-data"]');

  // Create a DataSet (allows two way data-binding)
  const items = new vis.DataSet([
    { id: 'segment1', content: "item 1", start: "2022-04-20" },
    { id: 'segment2', content: "item 4", start: "2023-01-16", end: "2023-03-08" },
    { id: 'segment6', content: "item 6", start: "2022-12-27", type: "point" }
  ]);

  // Configuration for the Timeline
  const options = {};

  // Create a Timeline
  const timeline = new vis.Timeline(container, items, options);
});
